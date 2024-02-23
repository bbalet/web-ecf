<?php

namespace App\ApiResource;

use App\State\RoomStateProvider;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

#[ApiResource(
    shortName: 'Room',
    description: 'List of rooms in theaters',
    provider: RoomStateProvider::class,
    paginationEnabled: false,
    operations: [
        new GetCollection(
            description: 'Get the list of the rooms in a theater',
            uriTemplate: '/theaters/{id}/rooms',
        ),
    ]
)]
class ApiRoom
{
    public ?int $id = null;

    public ?string $number = null;
}