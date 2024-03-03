<?php

namespace App\ApiResource;

use App\State\UserStateProvider;
use Symfony\Component\Serializer\Annotation\Ignore;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;

#[ApiResource(
    shortName: 'User',
    description: 'Information about connected user',
    provider: UserStateProvider::class,
    operations: [
        new Get(
            description: 'Get the information about the connected user',
            uriTemplate: '/whoami',
            security: "is_granted('ROLE_USER')"
        ),
    ]
)]
class ApiUser
{
    #[Ignore]
    public ?string $id = null;

    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $role = null;
}