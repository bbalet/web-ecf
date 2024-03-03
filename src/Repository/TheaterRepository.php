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

    /**
     * Return the list of theaters ordered by city
     *
     * @return array
     */
    public function findAllOrderByCity(): array
    {
        return $this->findBy([], ['city' => 'ASC']);
    }

    /**
     * Find the nearest theaters from a given latitude and longitude
     * the list is ordered by distance from the current position
     * the distance is limited to 10 km
     *
     * @param float $latitude lat of the current position
     * @param float $longitude long of the current position
     * @return array list of theaters ordered by distance from the current position
     */
    public function findNearestTheaters(float $latitude, float $longitude): array
    {
        $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN((:latitude1  - latitude) * pi()/180 / 2), 2) +COS( :latitude2 * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( :longitude - longitude) * pi()/180 / 2), 2) ))) as distance
                from theater
                having distance <= 10 order by distance";
        $dbConnection = $this->getEntityManager()->getConnection();
        $stmt = $dbConnection->prepare($sql);
        $stmt->bindValue('latitude1', $latitude);
        $stmt->bindValue('latitude2', $latitude);
        $stmt->bindValue('longitude', $longitude);
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
    }
}
