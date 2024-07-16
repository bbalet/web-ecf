<?php

namespace App\ApiResource;

use App\Filter\GeoFilter;
use ApiPlatform\Metadata\ApiProperty;
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
        new GetCollection()
    ]
)]
#[ApiFilter(GeoFilter::class, properties: ['latitude', 'longitude'])]
class ApiTheater
{
    /** Identifier of the theater */
    #[ApiProperty(identifier: true)]
    public ?int $theaterId = null;

    /** City where the theater is located */
    public ?string $city = null;

    /** Latitude of the postion of the theater */
    public ?float $latitude = null;

    /** Longitude of the postion of the theater */
    public ?float $longitude = null;
}