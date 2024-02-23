<?php

namespace App\State;

use App\Repository\IssueRepository;
use App\Repository\RoomRepository;
use App\ApiResource\ApiIssue;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\CollectionOperationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class IssueStateProvider implements ProviderInterface
{
    public function __construct(
        private IssueRepository $issueRepository,
        private RoomRepository $roomRepository,
        private Security $security
    ) {}

    /**
     * {@inheritDoc}
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        if ($user === null) {
            throw new AuthenticationException('Not authenticated or invalid token.');
        }
        if ($operation instanceof CollectionOperationInterface) {
            return $this->getListOIssues($uriVariables['id']);
        }
        return $this->getOneIssue($uriVariables['id']);
    }

    /**
     * Return the list of issues for a room
     *
     * @param [type] $context
     * @param integer $roomId Identifier of the room
     * @return array
     */
    private function getListOIssues(int $roomId): array
    {
        $issues = [];
        $dbIssues = $this->roomRepository->findOneBy(['id' => $roomId])->getIssues();

        foreach ($dbIssues as $dbIssue) {
            $issue = new ApiIssue();
            $issue->id = $dbIssue->getId();
            $issue->title = $dbIssue->getTitle();
            $issue->status = $dbIssue->getStatusAsString();
            $issue->description = $dbIssue->getDescription();
            $issues[] = $issue;
        }
        return $issues;
    }

    /**
     * Return an issue by its id
     *
     * @param string $sqId
     * @return ApiTicket
     */
    private function getOneIssue(string $issueId): ApiIssue
    {
        $dbIssue = $this->issueRepository->findOneBy(['id' => $issueId]);
        if (!$dbIssue) {
            throw new \Exception('Issue not found');
        } else {
            $issue = new ApiIssue();
            $issue->id = $dbIssue->getId();
            $issue->title = $dbIssue->getTitle();
            $issue->status = $dbIssue->getStatusAsString();
            $issue->description = $dbIssue->getDescription();
            return $issue;
        }
    }
}
