<?php

namespace App\Controller;

use Carbon\Carbon;
use App\Repository\GenreRepository;
use App\Repository\TheaterRepository;
use App\Repository\MovieRepository;
use App\Repository\MovieSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

class MoviesController extends AbstractController
{
    /**
     * Index page for Movies feature
     *
     * @return Response
     */
    #[Route('/movies', name: 'app_movies')]
    public function index(
        MovieSessionRepository $movieSessionRepository,
        GenreRepository $genreRepository,
        TheaterRepository $theaterRepository,
        #[MapQueryParameter] ?int $theaterId = null,
        #[MapQueryParameter] ?int $genreId = null,
        #[MapQueryParameter] ?int $dayNumber = null
        ): Response
    {
        // Get the days of the week
        $days = array();
        $weekdays = Carbon::getDays();
        for ($i=0; $i<7; $i++) {
            $days[$i] = ['id' => $i, 'name' => Carbon::create($weekdays[$i])->locale('fr')->dayName];
        }
        // If the parameters are set to default, set them to null
        if ($dayNumber == 8) {
            $dayNumber = null;  // Any day
        }
        if ($theaterId == 0) {
            $theaterId = null;  // Any theater
        }
        if ($genreId == 0) {
            $genreId = null;  // Any genre
        }
        
        // Get the theaters
        $theaters = $theaterRepository->findAll();
        // Get the genres
        $genres = $genreRepository->findAll();
        // Get all movies to be scheduled in the future by any theater
        $movies = $movieSessionRepository->findMoviesToBeScheduledInTheFuture($theaterId, $genreId, $dayNumber);
        return $this->render('movies/index.html.twig', [
            'theaterId' => $theaterId,
            'genreId' => $genreId,
            'dayNumber' => $dayNumber,
            'days' => $days,
            'theaters' => $theaters,
            'movies' => $movies,
            'genres' => $genres
        ]);
    }
}
