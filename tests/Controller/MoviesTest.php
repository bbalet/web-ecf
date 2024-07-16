<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\MovieRepository;

class MoviesTest extends WebTestCase
{
    public function testMoviesPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'movies');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('p[id=cboTheater]');
        $this->assertSelectorNotExists('p[id=cboGenre]');
        $this->assertSelectorNotExists('p[id=cboDay]');
    }

    public function testMoviePoster(): void
    {
        $client = static::createClient();
        $movieRepository = static::getContainer()->get(MovieRepository::class);
        $movie = $movieRepository->findAll()[0];
        $crawler = $client->request('GET', 'movies/' . $movie->getImdbId() . '/poster');
        $this->assertResponseIsSuccessful();
        
        // Try to assess if the poster is a valid image file
        $binaryData = $client->getInternalResponse()->getContent();
        $this->assertEquals("\xff\xd8\xff",substr($binaryData, 0, 3));
        /*
        "\xff\xd8\xff" => 'image/jpeg',
        "\x89PNG\x0d\x0a\x1a\x0a" => 'image/png',
        */
    }
}
