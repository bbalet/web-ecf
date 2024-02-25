<?php

namespace App\Repository;

use App\Entity\Seat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seat>
 *
 * @method Seat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seat[]    findAll()
 * @method Seat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seat::class);
    }

    /**
    * Return the disposition of seats for a given movie session:
    * - Is the seat booked?
    * - Is the reserved for people with disabilities?
    * 
    * @param integer $movieSessionId Identifier of the movie session
    * @return Seat[] Returns an array of Seat objects
    */
    public function getSeatsDisposition(int $movieSessionId): array
    {
        return $this->createQueryBuilder('Seat')
            ->select('Seat.id', 'Seat.number', 'Seat.for_disabled', 'Ticket.id as ticket_id')
            ->join('Seat.room', 'Room')
            ->join('App\Entity\MovieSession', 'MovieSession', 'WITH', 'MovieSession.room = Room.id')
            ->leftJoin('App\Entity\Ticket', 'Ticket', 'WITH', 'MovieSession.id = Ticket.movieSession AND Ticket.seat = Seat.id')
            ->andWhere('MovieSession.id = :movieSessionId')
            ->setParameter('movieSessionId', $movieSessionId)
            ->getQuery()
            ->getResult();
    }
}
