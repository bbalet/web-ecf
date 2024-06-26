<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
}
