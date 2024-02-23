<?php

namespace App\ApiResource;

use App\State\IssueStateProvider;
use App\State\IssueStateProcessor;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;

#[ApiResource(
    shortName: 'Issue',
    description: 'Issue Management. Restricted to employees.',
    paginationEnabled: false,
    operations: [
        new GetCollection(
            description: 'Get the list of issues for a room.',
            uriTemplate: '/rooms/{id}/issues',
            security: "is_granted('ROLE_EMPLOYEE')",
            provider: IssueStateProvider::class
        ),
        new Get(
            description: 'Get an issue by its id.',
            uriTemplate: '/issues/{id}',
            security: "is_granted('ROLE_EMPLOYEE')",
            provider: IssueStateProvider::class
        ),
        new Post(
            description: 'Create a new issue.',
            security: "is_granted('ROLE_EMPLOYEE')",
            uriTemplate: '/rooms/{id}/issues',
            processor: IssueStateProcessor::class
        ),
        new Patch(
            description: 'Update an existing issue.',
            security: "is_granted('ROLE_EMPLOYEE')",
            uriTemplate: '/issues/{id}',
            processor: IssueStateProcessor::class
        )
    ]
)]
class ApiIssue
{
    public ?int $id = null;

    public ?string $title = null;

    public ?string $status = null;

    public ?string $description = null;
}