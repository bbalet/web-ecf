<?php
namespace App\Tests;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * Test fonctionnel des endpoints de l'API Rencontre
 */
class ApiTheater extends ApiTestCase
{
    private Client $client;
    private JWTTokenManagerInterface $jwtManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        // Get the JWT Token Manager from the container
        $this->jwtManager = static::getContainer()->get(JWTTokenManagerInterface::class);
    }

    private function createAuthenticatedToken($user): string
    {
        return $this->jwtManager->create($user);
    }

    public function testGetTheaters(): void
    {
        $this->client->request('GET', '/api/theaters');
        $this->assertResponseStatusCodeSame(200);
        $content = $this->client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertGreaterThan(1, $data['hydra:totalItems']);
    }
}
