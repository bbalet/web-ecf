<?php

namespace App\Controller;

use App\Repository\TheaterRepository;
use App\Repository\MovieRepository;
use App\Repository\MovieSessionRepository;
use App\Repository\SeatRepository;
use App\Entity\OrderTickets;
use App\Entity\Ticket;
use App\Service\MongoDbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class BookingController extends AbstractController
{
    /**
     * Index page for Booking feature
     *
     * @return Response
     */
    #[Route('/booking', name: 'app_booking')]
    public function index(TheaterRepository $theaterRepository): Response
    {
        // Get all theaters
        $theaters = $theaterRepository->findAllOrderByCity();
        return $this->render('booking/index.html.twig', [
            'theaters' => $theaters
        ]);
    }

    /**
     * Booking page, when a theater is selected
     *
     * @return Response
     */
    #[Route('/booking/theaters/{theaterId}', name: 'app_booking_theater')]
    public function theater(int $theaterId, TheaterRepository $theaterRepository, MovieSessionRepository $movieSessionRepository): Response
    {
        // Get the theater
        $theater = $theaterRepository->findOneById($theaterId);
        // Get all movies
        //$movies = $movieSessionRepository->findMoviesScheduledInTheater($theaterId);
        $movies = $movieSessionRepository->findMoviesToBeScheduledInTheFutureByTheaterId($theaterId);

        return $this->render('booking/theater.html.twig', [
            'theater' => $theater,
            'movies' => $movies
        ]);
    }

    /**
     * Booking page, when a theater and a movie are selected
     *
     * @return Response
     */
    #[Route('/booking/theaters/{theaterId}/movies/{movieId}', name: 'app_booking_theater_movie')]
    public function movie(int $theaterId, int $movieId, TheaterRepository $theaterRepository,
        MovieSessionRepository $movieSessionRepository, MovieRepository $movieRepository): Response
    {
        // Get the theater
        $theater = $theaterRepository->findOneById($theaterId);
        // Get the movies
        $movie = $movieRepository->findOneById($movieId);
        // Get all sessions for the theater and the movie
        $sessions = $movieSessionRepository->findSessionsForTheaterAndMovie($theaterId, $movieId);

        return $this->render('booking/movie.html.twig', [
            'theater' => $theater,
            'movie' => $movie,
            'sessions' => $sessions
        ]);
    }

    /**
     * Booking page, when a theater and a movie are selected
     *
     * @return Response
     */
    #[Route('/booking/moviesessions/{movieSessionId}', name: 'app_booking_seats')]
    public function seats(int $movieSessionId, TheaterRepository $theaterRepository,
        MovieSessionRepository $movieSessionRepository, MovieRepository $movieRepository,
        SeatRepository $seatRepository): Response
    {
        // User needs to be authenticated to access the personal page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // Get the movie session
        $session = $movieSessionRepository->findOneById($movieSessionId);
        $room = $session->getRoom();
        if (is_null($session)) {
            throw $this->createNotFoundException('Séance non trouvée');
        }
        $sessionDetails = $movieSessionRepository->getSessionDetails($movieSessionId);

        // Get the theater
        $theater = $theaterRepository->findOneById($session->getRoom()->getTheater()->getId());
        // Get the movies
        $movie = $movieRepository->findOneById($session->getMovie()->getId());
        $seats = $seatRepository->getSeatsDisposition($movieSessionId);

        return $this->render('booking/seats.html.twig', [
            'theater' => $theater,
            'movie' => $movie,
            'session' => $session,
            'room' => $room,
            'sessionDetails' => $sessionDetails,
            'seats' => $seats
        ]);
    }

    /**
     * Booking page
     *
     * @return Response
     */
    #[Route('/booking/moviesessions/{movieSessionId}/seats/{seats}', name: 'app_booking_booking')]
    public function booking(int $movieSessionId, string $seats, MovieSessionRepository $movieSessionRepository,
        SeatRepository $seatRepository, EntityManagerInterface $entityManager, MongoDbService $mongoDbService): Response
    {
        // User needs to be authenticated to access the personal page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $seatIds = explode(",", $seats);
        $session = $movieSessionRepository->findOneById($movieSessionId);

        // Book the seats => Order and one ticket per seat
        $orderTicketsticket = new OrderTickets();
        $orderTicketsticket->setUser($user);
        $orderTicketsticket->setPurchaseDate(new \DateTime());
        $orderTicketsticket->setStatus(OrderTickets::STATUS_PAID);
        $entityManager->persist($orderTicketsticket);
        foreach ($seatIds as $seatId) {
            $seat = $seatRepository->findOneById($seatId);
            $ticket = new Ticket();
            $ticket->setMovieSession($session);
            $ticket->setSeat($seat);
            $ticket->setPrice($session->getRoom()->getQuality()->getPrice());
            $ticket->setOrderTickets($orderTicketsticket);
            $entityManager->persist($ticket);
        }
        $entityManager->flush();

        // Attempt to store the booking in MongoDB for later analysis
        $mongoDbService->storeBooking($session, $seatIds);

        $this->addFlash('success', 'Séance réservée avec succès !');
        return $this->redirectToRoute('app_userspace');
    }
}
