<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                EntityManagerInterface $entityManager, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $logger->debug('RegistrationController::register New user submitted.');
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $user->setRoles(['ROLE_USER']);
                $entityManager->persist($user);
                $entityManager->flush();
                $logger->debug('New user created: ' . $user->getEmail());

                // do anything else you need here, like send an email
                $email = (new TemplatedEmail())
                ->from('do-not-reply@cinephoria.cloud')
                ->to($user->getEmail())
                ->subject('Bienvenue sur Cinéphoria')
                ->text('Confirmation de création de compte sur Cinéphoria.')
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'firstname' => $user->getFirstname(),
                    'lastname' => $user->getLastname()
                ]);
                $mailer->send($email);
                $logger->debug('Email sent to ' . $user->getEmail() . ' for account creation confirmation.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création du compte utilisateur.');
                $logger->error('Error while creating user: ' . $e->getMessage());
                return $this->redirectToRoute('app_register');
            }
            $this->addFlash('success', 'Compte utilisateur créé avec succès !');
            return $this->redirectToRoute('app_home_page');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
