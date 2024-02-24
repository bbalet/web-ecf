<?php

namespace App\Repository;

use App\Entity\Quality;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quality>
 *
 * @method Quality|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quality|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quality[]    findAll()
 * @method Quality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QualityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quality::class);
    }
}
