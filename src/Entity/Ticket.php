<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MovieSession $movieSession = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OrderTickets $ordertickets = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seat $seat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getMovieSession(): ?MovieSession
    {
        return $this->movieSession;
    }

    public function setMovieSession(?MovieSession $movieSession): static
    {
        $this->movieSession = $movieSession;

        return $this;
    }

    public function getOrdertickets(): ?OrderTickets
    {
        return $this->ordertickets;
    }

    public function setOrdertickets(?OrderTickets $ordertickets): static
    {
        $this->ordertickets = $ordertickets;

        return $this;
    }

    public function getSeat(): ?Seat
    {
        return $this->seat;
    }

    public function setSeat(Seat $seat): static
    {
        $this->seat = $seat;

        return $this;
    }
}
