<?php
namespace App\Tests;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\UserRepository;

/**
 * Test fonctionnel de l'authentification JWT
 */
class ApiAuthenticationTest extends ApiTestCase
{
    public function testLogin(): void
    {
        $client = self::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('visistor1@example.org');

        // retrieve a token
        $response = $client->request('POST', '/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'visistor1@example.org',
                'password' => 'visistor1',
            ],
        ]);
        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }
}
