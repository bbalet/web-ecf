<?php

namespace App\State;

use App\Repository\IssueRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\ApiResource\ApiIssue;
use App\Entity\Issue;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Psr\Log\LoggerInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class IssueStateProcessor implements ProcessorInterface
{
    public function __construct(
        private IssueRepository $issueRepository,
        private RoomRepository $roomRepository,
        private Security $security,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    /**
     * {@inheritDoc}
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        if ($user === null) {
            throw new AuthenticationException('Not authenticated or invalid token.');
        }
        $this->logger->info('Processing an issue : ' . get_class($operation) . ' operation');
        
        if ($operation instanceof Post) {
            $this->logger->info('Creating a new issue');
            $roomId = $data->roomId;
            $this->logger->info('Creating a new issue for room ' . $roomId);
            $room = $this->roomRepository->findOneBy(['id' => $roomId]);
            if (!$room) {
                throw new NotFoundHttpException('Room #' . $roomId . ' not found.');
            } else { 
                $issue = new Issue();
                $issue->setUser($user);
                $issue->setRoom($room);
                $issue->setDate(new \DateTime());
                $issue->setTitle($data->title);
                $issue->setDescription($data->description);
                $issue->setStatus(Issue::STATUS_NEW);
                $this->entityManager->persist($issue);
                $this->entityManager->flush();
                //Return the updated issue from DB
                $issueApi = new ApiIssue();
                $issueApi->issueId = $issue->getId();
                $issueApi->title = $issue->getTitle();
                $issueApi->status = $issue->getStatusAsString();
                $issueApi->description = $issue->getDescription();
                return $issueApi;
            }
        } else if ($operation instanceof Patch) {
            $this->logger->info('Updating an issue');
            $issueId = $data->issueId;
            $this->logger->info('Updating issue ' . $issueId);
            $issue = $this->issueRepository->findOneBy(['id' => $issueId]);
            if (!$issue) {
                throw new NotFoundHttpException('Issue not found.');
            } else {            
                $issue->setTitle($data->title);
                $issue->setDescription($data->description);
                $issue->setStatus(intval($data->status));
                $this->entityManager->persist($issue);
                $this->entityManager->flush();
                //Return the updated issue from DB
                $issueApi = new ApiIssue();
                $issueApi->issueId = $issue->getId();
                $issueApi->title = $issue->getTitle();
                $issueApi->status = $issue->getStatusAsString();
                $issueApi->description = $issue->getDescription();
                return $issueApi;
            }
        }
    }
}