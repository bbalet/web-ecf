<?php

namespace App\ApiResource;

use App\Filter\TheaterFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use App\State\RoomStateProvider;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

#[ApiResource(
    shortName: 'Room',
    description: 'List of rooms in theaters',
    provider: RoomStateProvider::class,
    paginationEnabled: false,
    operations: [
        new GetCollection()
    ]
)]
#[ApiFilter(TheaterFilter::class, properties: ['theaterId'])]
class ApiRoom
{
    #[ApiProperty(identifier: true)]
    public ?int $roomId = null;

    public ?string $number = null;
}