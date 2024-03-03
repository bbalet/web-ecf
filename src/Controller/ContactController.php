<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $logger->debug('ContactController::contact New contact form submitted.');
                $formData = $form->getData();
                if ($formData['name'] === null) $formData['name'] = 'n/a';

                // do anything else you need here, like send an email
                $email = (new TemplatedEmail())
                ->from('do-not-reply@cinephoria.cloud')
                ->to($_ENV['EMAIL_ADMIN'])
                ->subject('[Cinéphoria][Contact web] ' . $formData['title'])
                ->text('Un formulaire de contact a été rempli sur le site web Cinéphoria.')
                ->htmlTemplate('contact/contact_email.html.twig')
                ->context([
                    'name' => $formData['name'],
                    'title' => $formData['title'],
                    'description' => $formData['description']
                ]);
                $mailer->send($email);
                $logger->debug('An email was sent following a contact request.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors .');
                $logger->error('Error while sending the contact request: ' . $e->getMessage());
                return $this->redirectToRoute('app_register');
            }
            $this->addFlash('success', 'Message envoyé avec succès !');
            return $this->redirectToRoute('app_home_page');
        }

        return $this->render('contact/contact.html.twig', [
            'contactForm' => $form,
        ]);
    }
}
