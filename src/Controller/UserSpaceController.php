<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderTicketsRepository;
use App\Repository\ReviewRepository;
use App\Repository\MovieRepository;

class UserSpaceController extends AbstractController
{
    /**
     * Display the user space
     *
     * @return Response
     */
    #[Route('/userspace', name: 'app_userspace')]
    public function index(OrderTicketsRepository $orderTicketsRepository): Response
    {
        // User needs to be authenticated to access the personal page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User */
        $user = $this->getUser();

        // Get all orders for the user
        $orders = $orderTicketsRepository->findOderTicketsByUser($user->getId());

        return $this->render('userspace/index.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/userspace/movies/{id}/review', name: 'app_userspace_movie_review')]
    public function review(int $movieId, MovieRepository $movieRepository, ReviewRepository $reviewRepository): Response
    {
        // User needs to be authenticated to access the personal page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User */
        $user = $this->getUser();

        // Get the movie to review and throw an error if it does not exist
        $movie = $movieRepository->findOneById($movieId);

        // TODO form
        // TODO update entity with a status (NEW, VALIDATED, REJECTED)
        // TODO the average of the ratings is calculated with VALIDATED reviews
        // TODO update the datafixtures scripts with all generated review to VALIDATED status

        return $this->render('userspace/review.html.twig');
    }

}
