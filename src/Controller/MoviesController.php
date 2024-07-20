<?php

namespace App\Controller;

use Carbon\Carbon;
use App\Repository\GenreRepository;
use App\Repository\TheaterRepository;
use App\Repository\MovieRepository;
use App\Repository\MovieSessionRepository;
use App\Form\MovieType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tmdb\Event\RequestEvent;
use Tmdb\Event\BeforeRequestEvent;
use Tmdb\Event\Listener\Request\AcceptJsonRequestListener;
use Tmdb\Event\Listener\Request\ApiTokenRequestListener;
use Tmdb\Event\Listener\Request\ContentTypeJsonRequestListener;
use Tmdb\Event\Listener\Request\UserAgentRequestListener;
use Tmdb\Event\Listener\RequestListener;
use Http\Client\Common\Plugin;
use Tmdb\Event\Listener\Psr6CachedRequestListener;
use Tmdb\Repository\MovieRepository as TmdbMovieRepository;
use Tmdb\Repository\FindRepository;
use Tmdb\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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


    /**
     * Retrun the movie poster. the movie poster can be stored in the public folder
     * (for the default dataset loaded by the fixtures) or in the cache folder.
     * If the movie poster is not found in either of these folders, an API call is made
     * to api.themoviedb.org (if the API key is set in the .env file). If the API key 
     * is not set, a default image is returned.
     * @param string $imdbId Movie ID on IMDB (e.g. 'tt1234567')
     * @return Response JPEG image
     */
    #[Route('/movies/{imdbId}/poster', name: 'app_movies_poster')]
    public function poster(string $imdbId, LoggerInterface $logger): Response
    {
        $filepath = $this->getParameter('kernel.project_dir') . '/public/posters/' . $imdbId . '.jpg';
        $defaultFilepath = $this->getParameter('kernel.project_dir') . '/public/posters/default.jpg';
        $fileContent = null;
        $apiKey = $_ENV['THEMOVIEDB_API_KEY'];

        // Check if the file exists in the public folder
        if (!file_exists($filepath)) {
            $logger->info('The file does not exist in the public folder.');
            // Check if we have an API key
            if ($apiKey == "") {
                // If we don't have a Tmdb API key, return a default image
                $logger->info('we do not have a Tmdb API key, return a default image.');
                $filepath = $this->getParameter('kernel.project_dir') . '/public/posters/default.jpg';
            } else {
                // check if the file exists in the cache folder or make the API call to themoviedb.org
                $logger->info('Let us build the Tmdb API Client.');
                $ed = new EventDispatcher();
                $client = new Client([
                    'api_token' => $apiKey,
                    'event_dispatcher' => [
                        'adapter' => $ed
                    ],
                ]);

                // Setup cache for the API calls
                $cache = new FilesystemAdapter('api.themoviedb.org', 0);
                $requestListener = new Psr6CachedRequestListener(
                    $client->getHttpClient(),
                    $client->getEventDispatcher(),
                    $cache,
                    $client->getHttpClient()->getPsr17StreamFactory(),
                    []
                );
                $requestListener = new RequestListener($client->getHttpClient(), $ed);
                $ed->addListener(RequestEvent::class, $requestListener);
                $apiTokenListener = new ApiTokenRequestListener($client->getToken());
                $ed->addListener(BeforeRequestEvent::class, $apiTokenListener);
                $acceptJsonListener = new AcceptJsonRequestListener();
                $ed->addListener(BeforeRequestEvent::class, $acceptJsonListener);
                $jsonContentTypeListener = new ContentTypeJsonRequestListener();
                $ed->addListener(BeforeRequestEvent::class, $jsonContentTypeListener);
                $userAgentListener = new UserAgentRequestListener();
                $ed->addListener(BeforeRequestEvent::class, $userAgentListener);
                
                // Find the movie by IMDb ID
                $findRepository = new FindRepository($client);
                $findResult = $findRepository->findBy($imdbId, ['external_source' => 'imdb_id']);
                if ($findResult->getMovieResults()->count() > 0) {
                    $movie = $findResult->getMovieResults()->getIterator()->current();
                    $posterPath = $movie->getPosterPath();
                    
                    if ($posterPath) {
                        $logger->info('Get image from cache or make the API call.');
                        $fileContent = $cache->get($imdbId, function() use ($posterPath, $logger) {
                            $baseUrl = "https://image.tmdb.org/t/p/original";
                            $posterUrl = $baseUrl . $posterPath;
                            $logger->info('Get the poster from ' . $posterUrl);
                            return file_get_contents($posterUrl);
                        });
                    } else {
                        $logger->info('Cannot getPosterPath().');
                        $fileContent = file_get_contents($defaultFilepath);
                    }
                } else {
                    $logger->info('Cannot find the movie by IMDb ID.');
                    $fileContent = file_get_contents($defaultFilepath);
                }
            }
        } else {
            $logger->info('The file exists in the public folder.');
            $fileContent = file_get_contents($filepath);
        }
        // retrun the image
        $response = new Response($fileContent);
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, basename($filepath));
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/png');
        return $response;
    }

    /**
     * Display or edit a movies
     *
     * @param int $id Identifier of the movie
     * @return Response
     */
    #[Route('/movies/{id}', name: 'app_movies_edit')]
    public function edit(int $id, MovieRepository $movieRepository): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Get the movie and throw an exception if it does not exist
        $movie = $movieRepository->findOneById($id);
        if (!$movie) {
            throw $this->createNotFoundException('Le film n\'existe pas.');
        }

        // Display the form to edit the movie
        $form = $this->createForm(MovieType::class, $movie);
        

        return $this->render('movies/edit.html.twig', [
            'currentPage' => 'movies',
            'form' => $form,
            'movie' => $movie
        ]);
    }

    /**
     * Delete a movie from the database
     *
     * @param int $id Identifier of the movie
     * @return Response
     */
    #[Route('/movies/{id}', name: 'app_movies_delete', methods: ["DELETE"])]
    public function delete(int $id, MovieRepository $movieRepository): Response
    {
        // Admin needs to be authenticated to access the admin pages
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Get the movie and throw an exception if it does not exist
        $movie = $movieRepository->findOneById($id);
        if (!$movie) {
            throw $this->createNotFoundException('Le film n\'existe pas.');
        }

        // Delete the movie
        $movieRepository->delete($movie);

        $this->addFlash('success', 'Film supprimé avec succès !');
        return $this->redirectToRoute('app_adminspace_movies');
    }
}
