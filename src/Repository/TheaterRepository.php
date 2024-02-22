<?php

namespace App\Repository;

use App\Entity\Theater;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Theater>
 *
 * @method Theater|null find($id, $lockMode = null, $lockVersion = null)
 * @method Theater|null findOneBy(array $criteria, array $orderBy = null)
 * @method Theater[]    findAll()
 * @method Theater[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TheaterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theater::class);
    }

    //    /**
    //     * @return Theater[] Returns an array of Theater objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Theater
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
