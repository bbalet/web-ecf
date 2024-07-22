<?php

namespace App\Repository;

use App\Entity\OrderTickets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderTicketsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderTickets::class);
    }

    /**
     * Find all orders for a given user. A subquery count the number of tickets for each order.
     *
     * @param [type] $userId The user id
     * @return void list of OrderTickets
     */
    public function findOderTicketsByUser($userId):array
    {
        return $this->createQueryBuilder('OrderTickets')
            ->select('OrderTickets.purchaseDate, Movie.id as movieId, Movie.title, MovieSession.startdate, COUNT(Ticket.id) as tickets, SUM(Ticket.price) as total')
            ->leftJoin('OrderTickets.tickets', 'Ticket')
            ->join('App\Entity\MovieSession', 'MovieSession', 'WITH', 'MovieSession.id = Ticket.movieSession')
            ->join('App\Entity\Movie', 'Movie', 'WITH', 'Movie.id = MovieSession.movie')
            ->where('OrderTickets.user = :userId')
            ->setParameter('userId', $userId)
            ->groupBy('OrderTickets.id, Movie.id, Movie.title, MovieSession.startdate')
            ->orderBy('OrderTickets.purchaseDate', 'DESC')
            ->getQuery()
            ->getResult();

    }
}
