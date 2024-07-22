<?php

namespace App\Controller;

use App\Repository\MovieSessionRepository;
use App\Repository\TicketRepository;
use App\Entity\MovieSession;
use App\Form\MovieSessionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class MovieSessionController extends AbstractController
{
    /**
     * Display or edit a movie session
     *
     * @param int $id Identifier of the movie
     * @return Response
     */
    #[Route('/moviesession/{id}', name: 'app_moviesession_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, MovieSessionRepository $movieSessionRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Admin needs to be authenticated to access the admin pages
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException();
        }

        // Get the movie and throw an exception if it does not exist
        $moviesession = $movieSessionRepository->findOneById($id);
        if (!$moviesession) {
            throw $this->createNotFoundException('La séance n\'existe pas.');
        }

        // Display the form to edit the movie
        $form = $this->createForm(MovieSessionType::class, $moviesession);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $moviesession = $form->getData();
            $entityManager->flush();
            $this->addFlash('success', 'La séance a été mise à jour');
            return $this->redirectToRoute('app_adminspace_sessions');
        }

        $tokenProvider = $this->container->get('security.csrf.token_manager');
        $token = $tokenProvider->getToken('delete-movie-session')->getValue();
        return $this->render('moviesession/edit.html.twig', [
            'currentPage' => 'sessions',
            'token' => $token,
            'form' => $form,
            'moviesession' => $moviesession
        ]);
    }

    /**
     * Create a new movie session
     *
     * @return Response
     */
    #[Route('/moviesession/create', name: 'app_moviesession_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Admin needs to be authenticated to access the admin pages
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException();
        }

        // Display the form to edit the movie session
        $moviesession = new MovieSession();
        $form = $this->createForm(MovieSessionType::class, $moviesession);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $moviesession = $form->getData();
            $entityManager->persist($moviesession);
            $entityManager->flush();
            $this->addFlash('success', 'La séance a été créée avec succès');
            return $this->redirectToRoute('app_adminspace_sessions');
        }

        return $this->render('moviesession/create.html.twig', [
            'currentPage' => 'sessions',
            'form' => $form
        ]);
    }


    /**
     * Delete a movie session from the database
     *
     * @param int $id Identifier of the movie
     * @return Response
     */
    #[Route('/moviesession/{id}/delete', name: 'app_moviesession_delete', methods: ["DELETE"])]
    public function delete(int $id, MovieSessionRepository $movieSessionRepository, TicketRepository $ticketRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Admin needs to be authenticated to access the admin pages
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException();
        }

        // Get the movie and throw an exception if it does not exist
        $moviesession = $movieSessionRepository->findOneById($id);
        if (!$moviesession) {
            throw $this->createNotFoundException('La séance n\'existe pas.');
        }

        //Is XSRF token valid ?
        $response = new Response();
        $token = $request->headers->get('X-CSRF-TOKEN');
        if (!$this->isCsrfTokenValid('delete-movie-session', $token)) {
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        // Delete the movie session and all its related entities
        $entityManager->beginTransaction();
        // List the ordered tickets linked to the movie session
        $tickets = $ticketRepository->findByMovieSession($moviesession->getId());
        foreach ($tickets as $ticket) {
            $entityManager->remove($ticket);
            $entityManager->flush();
            // orderdetails will be automatically removed as they are linked to the ticket
        }
        $entityManager->flush();
        $entityManager->remove($moviesession);
        $entityManager->flush();
        $entityManager->commit();
        $this->addFlash('success', 'La séance a été supprimée avec succès');
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}
