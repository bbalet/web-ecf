<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ReviewController extends AbstractController
{
    /**
     * Validate a review
     *
     * @param int $id Identifier of the movie
     * @return Response
     */
    #[Route('/review/{id}/validate', name: 'app_review_validate')]
    public function validate(int $id, ReviewRepository $reviewRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Employee needs to be authenticated to access the review pages
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        // Get the employee and throw an exception if it does not exist
        $review = $reviewRepository->findOneById($id);
        if (!$review) {
            throw $this->createNotFoundException('L\'avis n\'existe pas.');
        }
        $review->setValidated(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_adminspace_reviews');
    }

    /**
     * Unvalidate a review
     *
     * @param int $id Identifier of the movie
     * @return Response
     */
    #[Route('/review/{id}/unvalidate', name: 'app_review_unvalidate')]
    public function unvalidate(int $id, ReviewRepository $reviewRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Employee needs to be authenticated to access the review pages
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        // Get the employee and throw an exception if it does not exist
        $review = $reviewRepository->findOneById($id);
        if (!$review) {
            throw $this->createNotFoundException('L\'avis n\'existe pas.');
        }
        $review->setValidated(false);
        $entityManager->flush();
        return $this->redirectToRoute('app_adminspace_reviews');
    }

    /**
     * Rate a movie
     *
     * @param int $id Identifier of the movie
     * @return Response
     */
    #[Route('/review/{id}/rate', name: 'app_review_rate')]
    public function rate(int $id, ReviewRepository $reviewRepository, MovieRepository $movieRepository,
                Request $request, EntityManagerInterface $entityManager): Response
    {
        // User needs to be authenticated to access the personal page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User */
        $user = $this->getUser();

        $rating = $request->query->get('rating');
        $description = $request->query->get('description');

        if (!$rating || !$description) {
            return new Response('Invalid parameters', Response::HTTP_BAD_REQUEST);
        }

        // Check if the movie exists
        $movie = $movieRepository->findOneById($id);
        if (!$movie) {
            throw $this->createNotFoundException('Le film n\'existe pas.');
        }
        
        // Check if the user has already rated the movie
        $review = $reviewRepository->findOneBy(['user' => $user, 'movie' => $id]);
        if (!$review) {
            // If not create a new review
            $review = new Review();
        }

        $review->setRating((int) $rating);
        $review->setComment($description);
        $review->setMovie($movie);
        $review->setUser($user);
        $review->setValidated(false);
        $entityManager->persist($review);
        $entityManager->flush();
        return $this->redirectToRoute('app_userspace');
    }
}
