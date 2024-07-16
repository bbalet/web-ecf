<?php

namespace App\ApiResource;

use App\Filter\RoomFilter;
use App\State\IssueStateProvider;
use App\State\IssueStateProcessor;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;

#[ApiResource(
    shortName: 'Issue',
    description: 'Issue Management. Restricted to employees.',
    paginationEnabled: false,
    provider: IssueStateProvider::class,
    processor: IssueStateProcessor::class,
    security: "is_granted('ROLE_EMPLOYEE')",
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch()
    ]
)]
#[ApiFilter(RoomFilter::class, properties: ['roomId'])]
class ApiIssue
{
    #[ApiProperty(identifier: true)]
    public ?int $issueId = null;

    public ?int $roomId = null;

    public ?string $title = null;

    public ?string $status = null;

    public ?string $description = null;
}