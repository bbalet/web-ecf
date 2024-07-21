<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\TheaterRepository;
use App\Repository\MovieSessionRepository;
use App\Repository\UserRepository;

class BookingTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCinemaPageBreakdown(): void
    {
        $crawler = $this->client->request('GET', 'booking');
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains("Réservation Cinéphoria");
        $this->assertSelectorTextContains('h1', "Réserver une séance");
        $this->assertSelectorTextContains('h2', "Choisissez votre cinéma");

        $theaterRepository = static::getContainer()->get(TheaterRepository::class);
        $theater = $theaterRepository->findAll()[0];
        $crawler = $this->client->request('GET', '/booking/theaters/' . $theater->getId());
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains("Réservation Cinéphoria");
        $this->assertSelectorTextContains('h1', "Réserver une séance");
        $this->assertSelectorTextContains('h2', "Choisissez votre film");

        $movieSessionRepository = static::getContainer()->get(MovieSessionRepository::class);
        $movie = $movieSessionRepository->findMoviesScheduledInTheater($theater->getId())[0];
        $crawler = $this->client->request('GET', '/booking/theaters/' . $theater->getId() . 
                                                    '/movies/' . $movie['id']);
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains("Réservation Cinéphoria");
        $this->assertSelectorTextContains('h1', "Réserver une séance");
        $this->assertSelectorTextContains('h2', "Choisissez votre séance");

        // /booking/moviesessions/704  => we need to be logged in => check redirection
        $session = $movieSessionRepository->findSessionsForTheaterAndMovie($theater->getId(), $movie['id'])[0];
        $this->client->followRedirects(true);
        $crawler = $this->client->request('GET', '/booking/moviesessions/' . $session['movie_session_id']);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('visitor1@example.org');
        $this->assertSelectorTextContains('h2', "Connexion");
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/booking/moviesessions/' . $session['movie_session_id']);
        $this->assertPageTitleContains("Réservation Cinéphoria");
        $this->assertSelectorTextContains('h1', "Réserver une séance");
        $this->assertSelectorTextContains('h2', "Choisissez vos sièges");
    }
}
