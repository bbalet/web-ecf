<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    /**
     * Find all future sessions or of the current day for a given user
     * 
     * @param mixed $userId User Id
     * @return array
     */
    public function findAllFutureSessionsOrOfTheDay($userId): array
    {
         $dateTime = new \DateTime();
         return $this->createQueryBuilder('Ticket')
            ->join('Ticket.ordertickets', 'OrderTickets')
            ->join('Ticket.movieSession', 'MovieSession')
            ->andWhere('OrderTickets.user = :userId')
            ->andWhere('MovieSession.startdate >= :dateMin')
            ->setParameter('dateMin', $dateTime->format('Y-m-d 00:00:00'))
            ->setParameter('userId', $userId)
            ->orderBy('MovieSession.startdate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find a ticket by its id
     * 
     * @param [type] $id Ticket Id (not obfuscated id)
     * @return Ticket|null
     */
    public function findTicketById($id): ?Ticket
    {
        return $this->createQueryBuilder('Ticket')
            ->andWhere('Ticket.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
