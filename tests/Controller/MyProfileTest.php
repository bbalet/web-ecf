<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EspacePersonnelTest extends WebTestCase
{
    public function testEspacePersonnel(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        //Si l'utilisateur n'est pas connecté, il ne doit pas voir le lien vers son profil
        $crawler = $client->request('GET', '/');
        $this->assertSelectorNotExists('a[id=cmdMyProfile]');

        $testUser = $userRepository->findOneByEmail('visitor0@example.org');
        $client->loginUser($testUser);

        //Si l'utilisateur est connecté, il doit voir le lien vers son profil
        $crawler = $client->request('GET', '/');
        $this->assertSelectorExists('a[id=cmdMyProfile]');
        
        //Accès à la page de profil
        $crawler = $client->request('GET', '/userspace');
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains("Mon espace Cinéphoria");
        $this->assertSelectorTextContains('h1', 'Mon espace personnel');
        $this->assertSelectorTextContains('h2', 'Mes commandes');
    }
}
