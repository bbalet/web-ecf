<?php

namespace App\ApiResource;

use App\State\TheaterStateProvider;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

#[ApiResource(
    shortName: 'Theater',
    description: 'List of theaters',
    provider: TheaterStateProvider::class,
    paginationEnabled: false,
    operations: [
        new GetCollection(
            description: 'Get the list of theaters',
            uriTemplate: '/theaters',
        ),
    ]
)]
class ApiTheater
{
    public ?int $id = null;

    public ?string $city = null;
}