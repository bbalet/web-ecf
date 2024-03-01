<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSpaceController extends AbstractController
{
    /**
     * Display the admin space
     *
     * @return Response
     */
    #[Route('/adminspace', name: 'app_adminspace')]
    public function index(): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        /** @var User */
        $user = $this->getUser();

        // Get all orders for the user

        return $this->render('adminspace/index.html.twig', [
            'currentPage' => 'home'
        ]);
    }

    /**
     * Display the movies space
     *
     * @return Response
     */
    #[Route('/adminspace/movies', name: 'app_adminspace_movies')]
    public function movies(): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        /** @var User */
        $user = $this->getUser();

        // Get all orders for the user

        return $this->render('adminspace/movies.html.twig', [
            'currentPage' => 'movies'
        ]);
    }

    /**
     * Display the sessions space
     *
     * @return Response
     */
    #[Route('/adminspace/sessions', name: 'app_adminspace_sessions')]
    public function sessions(): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        /** @var User */
        $user = $this->getUser();

        // Get all orders for the user

        return $this->render('adminspace/sessions.html.twig', [
            'currentPage' => 'sessions'
        ]);
    }

    /**
     * Display the rooms space
     *
     * @return Response
     */
    #[Route('/adminspace/rooms', name: 'app_adminspace_rooms')]
    public function rooms(): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        /** @var User */
        $user = $this->getUser();

        // Get all orders for the user

        return $this->render('adminspace/rooms.html.twig', [
            'currentPage' => 'rooms'
        ]);
    }

    /**
     * Display the employees space
     *
     * @return Response
     */
    #[Route('/adminspace/employees', name: 'app_adminspace_employees')]
    public function employees(): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        /** @var User */
        $user = $this->getUser();

        // Get all orders for the user

        return $this->render('adminspace/employees.html.twig', [
            'currentPage' => 'employees'
        ]);
    }

    /**
     * Display the dashboard space
     *
     * @return Response
     */
    #[Route('/adminspace/dashboard', name: 'app_adminspace_dashboard')]
    public function dashboard(): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        /** @var User */
        $user = $this->getUser();

        // Get all orders for the user

        return $this->render('adminspace/dashboard.html.twig', [
            'currentPage' => 'dashboard'
        ]);
    }
}
