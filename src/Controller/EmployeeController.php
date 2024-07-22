<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\EmployeeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmployeeController extends AbstractController
{
    /**
     * Display or edit a employee
     *
     * @param int $id Identifier of the employee
     * @return Response
     */
    #[Route('/employees/{id}', name: 'app_employees_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Get the employee and throw an exception if it does not exist
        $employee = $userRepository->findOneById($id);
        if (!$employee) {
            throw $this->createNotFoundException('L\'employé n\'existe pas.');
        }

        // Display the form to edit the employee
        $form = $this->createForm(EmployeeType::class, $employee);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $employee = $form->getData();
            $hashedPassword = $passwordHasher->hashPassword(
                $employee,
                $employee->getPassword()
            );
            $employee->setPassword($hashedPassword);
            $entityManager->flush();
            $this->addFlash('success', 'L\'employé a été mis à jour');
            return $this->redirectToRoute('app_adminspace_employees');
        }

        $tokenProvider = $this->container->get('security.csrf.token_manager');
        $token = $tokenProvider->getToken('delete-employee')->getValue();
        return $this->render('employee/edit.html.twig', [
            'currentPage' => 'employees',
            'token' => $token,
            'form' => $form,
            'employee' => $employee
        ]);
    }

    /**
     * Create a new employee
     *
     * @return Response
     */
    #[Route('/employees/create', name: 'app_employees_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Display the form to edit the employee
        $employee = new User();
        $form = $this->createForm(EmployeeType::class, $employee);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $employee = $form->getData();
            $hashedPassword = $passwordHasher->hashPassword(
                $employee,
                $employee->getPassword()
            );
            $employee->setPassword($hashedPassword);
            $employee->setRoles(['ROLE_EMPLOYEE']);
            $entityManager->persist($employee);
            $entityManager->flush();
            $this->addFlash('success', 'L\'employé a été créé avec succès');
            return $this->redirectToRoute('app_adminspace_employees');
        }

        return $this->render('employee/create.html.twig', [
            'currentPage' => 'employees',
            'form' => $form
        ]);
    }


    /**
     * Delete a employee from the database
     *
     * @param int $id Identifier of the employee
     * @return Response
     */
    #[Route('/employees/{id}/delete', name: 'app_employees_delete', methods: ["DELETE"])]
    public function delete(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Get the employee and throw an exception if it does not exist
        $employee = $userRepository->findOneById($id);
        if (!$employee) {
            throw $this->createNotFoundException('L\'employé n\'existe pas.');
        }

        //Is XSRF token valid ?
        $response = new Response();
        $token = $request->headers->get('X-CSRF-TOKEN');
        if (!$this->isCsrfTokenValid('delete-employee', $token)) {
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        // Delete the employee
        $entityManager->remove($employee);
        $entityManager->flush();
        $this->addFlash('success', 'L\'employé a été supprimé avec succès');
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}
