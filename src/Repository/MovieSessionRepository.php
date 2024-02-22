<?php

namespace App\Repository;

use App\Entity\MovieSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MovieSession>
 *
 * @method MovieSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovieSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovieSession[]    findAll()
 * @method MovieSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovieSession::class);
    }

    //    /**
    //     * @return MovieSession[] Returns an array of MovieSession objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MovieSession
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
