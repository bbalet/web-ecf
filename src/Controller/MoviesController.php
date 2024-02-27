<?php

namespace App\Controller;

use App\Repository\TheaterRepository;
use App\Repository\MovieRepository;
use App\Repository\MovieSessionRepository;
use App\Repository\SeatRepository;
use App\Entity\OrderTickets;
use App\Entity\Ticket;
use Exception;
use MongoDB\Client;
use MongoDB\Driver\ServerApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class MoviesController extends AbstractController
{
    /**
     * Index page for Movies feature
     *
     * @return Response
     */
    #[Route('/movies', name: 'app_movies')]
    public function index(MovieSessionRepository $movieSessionRepository): Response
    {
        // Get all movies to be scheduled in the future by any theater
        $movies = $movieSessionRepository->findMoviesToBeScheduledInTheFuture();
        return $this->render('movies/index.html.twig', [
            'movies' => $movies
        ]);
    }
}
