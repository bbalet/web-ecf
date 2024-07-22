<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testContactPage(): void
    {
        $crawler = $this->client->request('GET', 'contact');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('p[id=cmdLogout]');
        $this->assertPageTitleContains("Formulaire de contact");
        $this->assertSelectorTextContains('h2', "Formulaire de contact");
    }
}
