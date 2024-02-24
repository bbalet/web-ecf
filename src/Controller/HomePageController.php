<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;

class HomePageController extends AbstractController
{
    /**
     * Display the home page
     *
     * @return Response
     */
    #[Route('/', name: 'app_home_page')]
    public function index(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findMoviesAddedLastWednesday();
        return $this->render('home_page/index.html.twig', [
            'movies' => $movies,
        ]);
    }
}
