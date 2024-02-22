<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?int $columns = null;

    #[ORM\OneToMany(targetEntity: Seat::class, mappedBy: 'room', orphanRemoval: true)]
    private Collection $seats;

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Theater $theater = null;

    #[ORM\OneToMany(targetEntity: Issue::class, mappedBy: 'room', orphanRemoval: true)]
    private Collection $issues;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quality $quality = null;

    public function __construct()
    {
        $this->seats = new ArrayCollection();
        $this->issues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getColumns(): ?int
    {
        return $this->columns;
    }

    public function setColumns(int $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return Collection<int, Seat>
     */
    public function getSeats(): Collection
    {
        return $this->seats;
    }

    public function addSeat(Seat $seat): static
    {
        if (!$this->seats->contains($seat)) {
            $this->seats->add($seat);
            $seat->setRoom($this);
        }

        return $this;
    }

    public function removeSeat(Seat $seat): static
    {
        if ($this->seats->removeElement($seat)) {
            // set the owning side to null (unless already changed)
            if ($seat->getRoom() === $this) {
                $seat->setRoom(null);
            }
        }

        return $this;
    }

    public function getTheater(): ?Theater
    {
        return $this->theater;
    }

    public function setTheater(?Theater $theater): static
    {
        $this->theater = $theater;

        return $this;
    }

    /**
     * @return Collection<int, Issue>
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    public function addIssue(Issue $issue): static
    {
        if (!$this->issues->contains($issue)) {
            $this->issues->add($issue);
            $issue->setRoom($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): static
    {
        if ($this->issues->removeElement($issue)) {
            // set the owning side to null (unless already changed)
            if ($issue->getRoom() === $this) {
                $issue->setRoom(null);
            }
        }

        return $this;
    }

    public function getQuality(): ?Quality
    {
        return $this->quality;
    }

    public function setQuality(?Quality $quality): static
    {
        $this->quality = $quality;

        return $this;
    }
}
