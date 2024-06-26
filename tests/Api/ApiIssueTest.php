<?php
namespace App\Tests;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Repository\UserRepository;
use App\Repository\TheaterRepository;
use App\Repository\RoomRepository;
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

    public function testPostIssue(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('employee@example.org');
        $token = $this->createAuthenticatedToken($user);
        $roomRepository = static::getContainer()->get(RoomRepository::class);
        $rooms = $roomRepository->findAll();
        $this->client->request('POST', '/api/issues', [
            'auth_bearer' => $token,
            'json' => [
                'roomId' => $rooms[0]->getId(),
                'title' => 'Test Issue API',
                'status' => 'Nouveau',
                'description' => 'Issue created by PHPUnit',
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);   // 201 = Created
        $content = $this->client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertArrayHasKey('@id', $data);
        $issueId = $data['issueId'];
        $issueRepository = static::getContainer()->get(IssueRepository::class);
        $issue = $issueRepository->findOneById($issueId);
        $this->assertEquals($issue->getId(), $data['issueId']);
        $this->assertEquals($issue->getStatusAsString(), $data['status']);
        $this->assertEquals('Nouveau', $data['status']);
    }

    public function testPatchIssue(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('employee@example.org');
        $token = $this->createAuthenticatedToken($user);
        $issueRepository = static::getContainer()->get(IssueRepository::class);
        $issue = $issueRepository->findAll()[0];
        $randStr = substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
        $this->client->request('PATCH', '/api/issues/' . $issue->getId(), [
            'auth_bearer' => $token,
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
            'json' => [
                'issueId' => $issue->getId(),
                'roomId' => $issue->getRoom()->getId(),
                'title' => $randStr,
                'status' => 'Ouvert',
                'description' => 'Issue patched by PHPUnit',
            ],
        ]);
        $this->assertResponseStatusCodeSame(200);   // 200 = Modified
        $content = $this->client->getResponse()->getContent();
        $data = json_decode($content, true);
        $issue = $issueRepository->findOneById($data['issueId']);
        $this->assertEquals($issue->getTitle(), $randStr);
        $this->assertEquals($issue->getStatusAsString(), $data['status']);
        $this->assertEquals('Ouvert', $data['status']);
    }
}
