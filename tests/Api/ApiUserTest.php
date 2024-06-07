<?php
namespace App\Tests;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Repository\UserRepository;

/**
 * Test fonctionnel des endpoints de l'API Rencontre
 */
class ApiUserTest extends ApiTestCase
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

    public function testWhoAmI(): void
    {
        // Get a user from the database
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('visitor5@example.org');
        $token = $this->createAuthenticatedToken($testUser);
        $this->client->request('GET', '/api/whoami', [
            'auth_bearer' => $token
        ]);
        $this->assertResponseStatusCodeSame(200);
        $content = $this->client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertEquals($testUser->getFirstname(), $data['firstName']);
        $this->assertEquals($testUser->getLastname(), $data['lastName']);
        $this->assertEquals("ROLE_USER", $data['role']);
    }

    public function testWhoAmITokenNotFound(): void
    {
        $this->client->request('GET', '/api/whoami');
        $this->assertResponseStatusCodeSame(401);
    }
}
