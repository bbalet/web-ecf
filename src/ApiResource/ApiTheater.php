<?php

namespace App\ApiResource;

use App\Filter\GeoFilter;
use App\State\TheaterStateProvider;
use ApiPlatform\Metadata\ApiFilter;
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
#[ApiFilter(GeoFilter::class, properties: ['latitude', 'longitude'])]
class ApiTheater
{
    /** Identifier of the theater */
    public ?int $id = null;

    /** City where the theater is located */
    public ?string $city = null;

    /** Latitude of the postion of the theater */
    public ?float $latitude = null;

    /** Longitude of the postion of the theater */
    public ?float $longitude = null;
}