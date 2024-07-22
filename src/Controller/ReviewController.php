<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
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
     * @param int $id Identifier of the employee
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
     * @param int $id Identifier of the employee
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
}
