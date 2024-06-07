<?php
namespace App\Tests;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use Sqids\Sqids;


/**
 * Test fonctionnel des endpoints de l'API Cinéphoria / Ticket
 */
class ApiTicketTest extends ApiTestCase
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

    public function testTicketsOfAUser(): void
    {
        // Get a user from the database
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('visitor1@example.org');
        $token = $this->createAuthenticatedToken($testUser);
        $this->client->request('GET', '/api/tickets', [
            'auth_bearer' => $token
        ]);
        $this->assertResponseStatusCodeSame(200);
        $content = $this->client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertGreaterThan(1, $data['hydra:totalItems']);
    }

    public function testGetTicketObfuscatedId(): void
    {
        // Get a user from the database
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('visitor1@example.org');
        $token = $this->createAuthenticatedToken($testUser);

        // Get a ticket from the database and obfuscate its id
        $ticketRepository = static::getContainer()->get(TicketRepository::class);
        $tickets = $ticketRepository->findAllFutureSessionsOrOfTheDay($testUser->getId());
        $sqids = new Sqids();
        $obfuscatedId = $sqids->encode([$tickets[0]->getId()]);
        $this->client->request('GET', '/api/tickets/' . $obfuscatedId, [
            'auth_bearer' => $token
        ]);
        $this->assertResponseStatusCodeSame(200);
        $content = $this->client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertEquals($obfuscatedId, $data['ticketId']);
        $this->assertEquals($tickets[0]->getMovieSession()->getMovie()->getImdbId(), $data['imdbId']);
    }

}
