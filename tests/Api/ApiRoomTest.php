<?php
namespace App\Tests;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Repository\TheaterRepository;

/**
 * Test fonctionnel des endpoints de l'API Cinéphoria / Salles
 */
class ApiRoomTest extends ApiTestCase
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

    public function testGetRoomsOfATheater(): void
    {
        $theaterRepository = static::getContainer()->get(TheaterRepository::class);
        $theater = $theaterRepository->findAll()[0];
        $this->client->request('GET', '/api/rooms?theaterId=' . $theater->getId());
        $this->assertResponseStatusCodeSame(200);
        $content = $this->client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertGreaterThan(1, $data['hydra:totalItems']);
    }

    public function testGetRoomsOfAnInvalidTheater(): void
    {
        $this->client->request('GET', '/api/rooms?theaterId=99999999');
        $this->assertResponseStatusCodeSame(500);
    }
}
