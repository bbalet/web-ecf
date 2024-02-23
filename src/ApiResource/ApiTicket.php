<?php

namespace App\ApiResource;

use App\Entity\User;
use App\State\TicketStateProvider;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;

#[ApiResource(
    shortName: 'Ticket',
    description: 'My tickets',
    provider: TicketStateProvider::class,
    paginationEnabled: false,
    operations: [
        new GetCollection(
            description: 'Get the list of future tickets for the current user',
            uriTemplate: '/tickets',
            normalizationContext: ['groups' => 'ticket:list']
        ),
        new Get(
            description: 'Get a ticket by its id. Allow an employee to access any ticket. Allow a user to access only his tickets.',
            uriTemplate: '/tickets/{id}',
            normalizationContext: ['groups' => 'ticket:item'],
            security: "is_granted('ROLE_EMPLOYEE') or object.owner == user"
        ),
    ]
)]
class ApiTicket
{
    #[Groups(['ticket:list', 'ticket:item'])]
    public ?string $id = null;

    #[Groups(['ticket:list', 'ticket:item'])]
    public ?string $imdbId = null;

    #[Groups(['ticket:list', 'ticket:item'])]
    public ?string $movieTitle = null;

    #[Groups(['ticket:list', 'ticket:item'])]
    public ?string $day = null;

    #[Groups(['ticket:list', 'ticket:item'])]
    public ?string $roomNumber = null;

    #[Groups(['ticket:list', 'ticket:item'])]
    public ?\DateTimeInterface $startDate = null;

    #[Groups(['ticket:list', 'ticket:item'])]
    public ?\DateTimeInterface $endDate = null;

    #[ApiProperty(security: "object.owner == user")]
    #[Groups(['ticket:item'])]
    public ?string $qrCode = null;

    #[ApiProperty(security: "is_granted('ROLE_EMPLOYEE')")]
    #[Groups(['ticket:item'])]
    public ?string $nbTicketsInOrder = null;

    #[ApiProperty(security: "is_granted('ROLE_EMPLOYEE')")]
    #[Groups(['ticket:item'])]
    public ?string $firstName = null;

    #[ApiProperty(security: "is_granted('ROLE_EMPLOYEE')")]
    #[Groups(['ticket:item'])]
    public ?string $lastName = null;

    #[Ignore]
    public ?User $owner = null;
}