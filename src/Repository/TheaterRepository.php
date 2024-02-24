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
}
