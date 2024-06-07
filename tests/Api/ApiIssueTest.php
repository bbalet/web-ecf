<?php
namespace App\Tests;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Repository\UserRepository;
use App\Repository\TheaterRepository;
use App\Repository\IssueRepository;

/**
 * Test fonctionnel des endpoints de l'API Cinéphoria / Issues
 */
class ApiIssueTest extends ApiTestCase
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

    public function testIssuesOfARoom(): void
    {
        // Get a user from the database
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('employee@example.org');
        $token = $this->createAuthenticatedToken($testUser);

        // Find a room
        $theaterRepository = static::getContainer()->get(TheaterRepository::class);
        $theater = $theaterRepository->findAll()[0];
        $room = $theater->getRooms()[0];

        $this->client->request('GET', '/api/issues?roomId=' . $room->getId(), [
            'auth_bearer' => $token
        ]);
        $this->assertResponseStatusCodeSame(200);
        $content = $this->client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertGreaterThan(1, $data['hydra:totalItems']);
    }

    public function testOneIssue(): void
    {
        // Get a user from the database
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('employee@example.org');
        $token = $this->createAuthenticatedToken($testUser);

        // Find an issue
        $issueRepository = static::getContainer()->get(IssueRepository::class);
        $issue = $issueRepository->findAll()[0];

        $this->client->request('GET', '/api/issues/' . $issue->getId(), [
            'auth_bearer' => $token
        ]);
        $this->assertResponseStatusCodeSame(200);
        $content = $this->client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertEquals($issue->getId(), $data['issueId']);
        $this->assertEquals($issue->getTitle(), $data['title']);
        $this->assertEquals($issue->getRoom()->getId(), $data['roomId']);
    }

    public function testAccessDenied(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('visitor1@example.org');
        $token = $this->createAuthenticatedToken($testUser);

        // Find a room
        $theaterRepository = static::getContainer()->get(TheaterRepository::class);
        $theater = $theaterRepository->findAll()[0];
        $room = $theater->getRooms()[0];

        $this->client->request('GET', '/api/issues?roomId=' . $room->getId(), [
            'auth_bearer' => $token
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

}
