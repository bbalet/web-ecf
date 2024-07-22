<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     * Return the list of movies ordered by title
     *
     * @return array
     */
    public function findAllOrderByTitle(): array
    {
        return $this->findBy([], ['title' => 'ASC']);
    }

    /**
    * Return the list of movies that were added wednesday last week
    * @return MovieSession[] Returns an array of MovieSession objects
    */
    public function findMoviesAddedLastWednesday(): array
    {
        $lastWednesday = new \DateTime('wednesday last week');
        return $this->createQueryBuilder('Movie')
            ->andWhere('Movie.dateAdded BETWEEN :dateMin AND :dateMax')
            ->setParameter('dateMin', $lastWednesday->format('Y-m-d 00:00:00'))
            ->setParameter('dateMax', $lastWednesday->format('Y-m-d 23:59:59'))
            ->orderBy('Movie.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the average score of a movie
     *
     * @param [type] $id movie id
     * @return integer|null
     */
    public function getAverageScore($id): ?int
    {
        return $this->createQueryBuilder('review')
            ->select('AVG(review.rating) as average')
            ->andWhere('review.movie_id = :id')
            ->andWhere('review.validated is true')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
