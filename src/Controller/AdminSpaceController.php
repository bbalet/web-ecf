<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;
use App\Service\MongoDbService;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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


        return $this->render('adminspace/index.html.twig', [
            'currentPage' => 'home'
        ]);
    }

    /**
     * Display the list of movies
     *
     * @return Response
     */
    #[Route('/adminspace/movies', name: 'app_adminspace_movies')]
    public function movies(MovieRepository $movieRepository): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Get all movies
        $movies = $movieRepository->findAllOrderByTitle();

        return $this->render('adminspace/movies.html.twig', [
            'currentPage' => 'movies',
            'movies' => $movies
        ]);
    }

    /**
     * Display the edit page of a movies
     *
     * @param int $id Identifier of the movie
     * @return Response
     */
    #[Route('/adminspace/movies/{id}', name: 'app_adminspace_movie_edit')]
    public function movieEdit(int $id, MovieRepository $movieRepository): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Get all movies
        $movie = $movieRepository->findOneById($id);

        return $this->render('adminspace/movies.edit.html.twig', [
            'currentPage' => 'movies',
            'movie' => $movie
        ]);
    }

    /**
     * Display the list of sessions
     *
     * @return Response
     */
    #[Route('/adminspace/sessions', name: 'app_adminspace_sessions')]
    public function sessions(): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');


        return $this->render('adminspace/sessions.html.twig', [
            'currentPage' => 'sessions'
        ]);
    }

    /**
     * Display the list of rooms
     *
     * @return Response
     */
    #[Route('/adminspace/rooms', name: 'app_adminspace_rooms')]
    public function rooms(): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');


        return $this->render('adminspace/rooms.html.twig', [
            'currentPage' => 'rooms'
        ]);
    }

    /**
     * Display the list of employees
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
    public function dashboard(MongoDbService $mongoDbService, ChartBuilderInterface $chartBuilder): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $availability = $mongoDbService->isAvailable();
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        if ($availability['status']) {
            $bookings = $mongoDbService->queryBookings();
            $chart->setData([
                'labels' => array_keys($bookings),
                'datasets' => [
                    [
                        'label' => "Nombre de tickets vendus",
                        'data' => array_values($bookings),
                    ],
                ],
            ]);
            // Add numbers of tickets sold for each bar of the chart
            $chart->setOptions([
                'plugins' => [
                    'datalabels' => [
                        'color' => 'white',
                        'font' => [
                            'weight' => 'bold'
                        ],
                    ],
                ],
            ]);
        }


        return $this->render('adminspace/dashboard.html.twig', [
            'currentPage' => 'dashboard',
            'availability' => $availability,
            'chart' => $chart
        ]);
    }
}
