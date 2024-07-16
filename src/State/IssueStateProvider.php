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
            return $this->getListOIssues($context);
        }
        return $this->getOneIssue($uriVariables['issueId']);
    }

    /**
     * Return the list of issues for a given room
     *
     * @param mixed $context can be used to pass filter on the room
     * @param [type] $context
     * @return array
     */
    private function getListOIssues($context): array
    {
        $roomId = $context['filters']['roomId'];
        $issues = [];
        $dbIssues = $this->roomRepository->findOneBy(['id' => $roomId])->getIssues();

        foreach ($dbIssues as $dbIssue) {
            $issue = new ApiIssue();
            $issue->roomId = $dbIssue->getRoom()->getId();
            $issue->issueId = $dbIssue->getId();
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
     * @param string $issueId
     * @return ApiTicket
     */
    private function getOneIssue(string $issueId): ApiIssue
    {
        $dbIssue = $this->issueRepository->findOneBy(['id' => $issueId]);
        if (!$dbIssue) {
            throw new \Exception('Issue #' . $issueId . ' not found');
        } else {
            $issue = new ApiIssue();
            $issue->issueId = $dbIssue->getId();
            $issue->roomId = $dbIssue->getRoom()->getId();
            $issue->title = $dbIssue->getTitle();
            $issue->status = $dbIssue->getStatusAsString();
            $issue->description = $dbIssue->getDescription();
            return $issue;
        }
    }
}
