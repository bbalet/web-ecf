<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;
use App\Repository\MovieSessionRepository;
use App\Repository\RoomRepository;
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
     * Display the list of sessions
     *
     * @return Response
     */
    #[Route('/adminspace/sessions/{movieId}', name: 'app_adminspace_sessions', defaults: ['movieId' => -1])]
    public function sessions(int $movieId, MovieSessionRepository $movieSessionRepository): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($movieId === -1) {
            $sessions = $movieSessionRepository->findAll();
        } else {
            $sessions = $movieSessionRepository->findBy(['movie' => $movieId]);
        }

        return $this->render('adminspace/sessions.html.twig', [
            'currentPage' => 'sessions',
            'movieId' => $movieId,
            'sessions' => $sessions
        ]);
    }

    /**
     * Display the list of rooms
     *
     * @return Response
     */
    #[Route('/adminspace/rooms', name: 'app_adminspace_rooms')]
    public function rooms(RoomRepository $roomRepository): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Get all rooms
        $rooms = $roomRepository->findAllOrderByTheaterAndNumber();

        return $this->render('adminspace/rooms.html.twig', [
            'currentPage' => 'rooms',
            'rooms' => $rooms
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

        

        return $this->render('adminspace/employees.html.twig', [
            'currentPage' => 'employees'
        ]);
    }

    /**
     * Display the dashboard of the quantity of tickets sold by movie
     * this is a bar chart built from a NoSQL database
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
