<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use App\Entity\Room;
use App\Form\RoomType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class RoomController extends AbstractController
{
    /**
     * Display or edit a room
     *
     * @param int $id Identifier of the movie
     * @return Response
     */
    #[Route('/room/{id}', name: 'app_room_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, RoomRepository $roomRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Admin needs to be authenticated to access the admin pages
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException();
        }

        // Get the movie and throw an exception if it does not exist
        $room = $roomRepository->findOneById($id);
        if (!$room) {
            throw $this->createNotFoundException('La salle n\'existe pas.');
        }

        // Display the form to edit the movie
        $form = $this->createForm(RoomType::class, $room);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $room = $form->getData();
            $entityManager->flush();
            $this->addFlash('success', 'La salle a été mise à jour');
            return $this->redirectToRoute('app_adminspace_rooms');
        }

        return $this->render('room/edit.html.twig', [
            'currentPage' => 'sessions',
            'form' => $form,
            'room' => $room
        ]);
    }

    /**
     * Create a new room
     *
     * @return Response
     */
    #[Route('/room/create', name: 'app_room_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Admin needs to be authenticated to access the admin pages
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException();
        }

        // Display the form to edit the movie session
        $room = new room();
        $form = $this->createForm(RoomType::class, $room);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $room = $form->getData();
            $entityManager->persist($room);
            $entityManager->flush();
            $this->addFlash('success', 'La salle a été créée avec succès');
            return $this->redirectToRoute('app_adminspace_rooms');
        }

        return $this->render('room/create.html.twig', [
            'currentPage' => 'rooms',
            'form' => $form
        ]);
    }
}
