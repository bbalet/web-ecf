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

    public function findMoviesToBeScheduledInTheFuture(): array
    {
        $dbConnection = $this->getEntityManager()->getConnection();
        $subSelect = $dbConnection->createQueryBuilder()
            ->select('movie_id', 'avg(rating) as movie_avg_rating')
            ->from('review') 
            ->groupBy('movie_id')->getSQL();

        $today = new \DateTime();
        $q = $dbConnection->createQueryBuilder()
            ->from('movie_session')
            ->select('movie.id, movie.imdb_id, movie.title, movie.description, movie.minimum_age, movie.is_team_favorite, movie_avg_rating as rating')
            ->join('movie_session', 'movie', 'ON movie_session.movie_id = movie.id')
            ->leftJoin('movie_session', sprintf('(%s)', $subSelect), 'reviews', 'movie_session.movie_id = reviews.movie_id')
            ->andWhere('movie_session.startdate > :startdate')
            ->setParameter('startdate', $today->format('Y-m-d H:i:s'))
            ->groupBy('movie.id, movie.imdb_id, movie.title, movie.description, movie.minimum_age, movie.is_team_favorite, movie_avg_rating')
            ->addOrderBy('movie.date_added', 'DESC');

        return $q->executeQuery()->fetchAllAssociative();
    }

    /**
     * Return the list of movies to be scheduled in the future for a given theater
     * regardless if they are fully booked or not
     *
     * @param integer $theaterId
     * @return array
     */
    public function findMoviesToBeScheduledInTheFutureByTheaterId(int $theaterId): array
    {
        $movies = $this->findMoviesScheduledInTheater($theaterId);
        $moviesToBeScheduled = [];
        foreach ($movies as $movie) {
            array_push($moviesToBeScheduled, ['id' => $movie['id'], 'title' => $movie['title']]);
        }
        $moviesToBeScheduled = array_unique($moviesToBeScheduled, SORT_REGULAR);
        return $moviesToBeScheduled;
    }

    /**
    * Return the list of movies with a session today or in the future
    * for a given theater
    *
    * @param integer $theaterId Identifier of the theater
    * @return MovieSession[] Returns an array of MovieSession objects
    */
    public function findMoviesScheduledInTheater(int $theaterId): array
    {
        // Total number of booked seats for each session
        $dbConnection = $this->getEntityManager()->getConnection();
        $subSelect = $dbConnection->createQueryBuilder()
            ->select('movie_session_id', 'sum(1) as booked_seats')
            ->from('ticket') 
            ->groupBy('movie_session_id')->getSQL();

        $today = new \DateTime();
        $q = $dbConnection->createQueryBuilder()
            ->select('movie.title, movie.id')
            ->from('movie_session')
            ->join('movie_session', 'movie', 'ON movie_session.movie_id = movie.id')
            ->join('movie_session', 'room', 'ON movie_session.room_id = room.id')
            ->join('movie_session', 'theater', 'ON room.theater_id = theater.id')
            ->leftJoin('movie_session', sprintf('(%s)', $subSelect), 'bookings', 'movie_session.id = bookings.movie_session_id')
            ->andWhere('theater.id = :theaterId')
            ->setParameter('theaterId', $theaterId)
            ->andWhere('movie_session.startdate > :startdate')
            ->setParameter('startdate', $today->format('Y-m-d H:i:s'))
            ->groupBy('movie.id, movie.title')
            ->addOrderBy('movie.title', 'ASC');
        
        return $q->executeQuery()->fetchAllAssociative();
    }

    /**
    * Return the list of future sessions for a given theater and movie
    * 
    * @param integer $theaterId Identifier of the theater
    * @param integer $movieId Identifier of the movie
    * @return MovieSession[] Returns an array of MovieSession objects
    */
    public function findSessionsForTheaterAndMovie(int $theaterId, int $movieId): array
    {
        // Total number of booked seats for each session
        $dbConnection = $this->getEntityManager()->getConnection();
        $subSelect = $dbConnection->createQueryBuilder()
            ->select('movie_session_id', 'sum(1) as booked_seats')
            ->from('ticket')
            ->groupBy('movie_session_id')->getSQL();

        $today = new \DateTime();
        $q = $dbConnection->createQueryBuilder()
            ->select('movie_session_id, movie_session.startdate, movie_session.enddate, booked_seats, room.capacity, (room.capacity-booked_seats) as seats_left, quality.name, quality.price')
            ->from('movie_session')
            ->join('movie_session', 'movie', 'ON movie_session.movie_id = movie.id')
            ->join('movie_session', 'room', 'ON movie_session.room_id = room.id')
            ->join('movie_session', 'quality', 'ON room.quality_id = quality.id')
            ->join('movie_session', 'theater', 'ON room.theater_id = theater.id')
            ->leftJoin('movie_session', sprintf('(%s)', $subSelect), 'bookings', 'movie_session.id = bookings.movie_session_id')
            ->andWhere('theater.id = :theaterId')
            ->setParameter('theaterId', $theaterId)
            ->andWhere('movie.id = :movieId')
            ->setParameter('movieId', $movieId)
            ->andWhere('movie_session.startdate > :startdate')
            ->setParameter('startdate', $today->format('Y-m-d H:i:s'))
            ->groupBy('movie_session_id, movie.id, movie_session.startdate, movie_session.enddate, movie.title, booked_seats, room.capacity, quality.name, quality.price')
            ->orderBy('movie_session.startdate', 'ASC');

            return $q->executeQuery()->fetchAllAssociative();
    }

    /**
    * Return the list of future sessions for a given theater and movie
    * 
    * @param integer $movieSessionId Identifier of the movie session
    * @return MovieSession[] Returns an array of MovieSession objects
    */
    public function getSessionDetails(int $movieSessionId): array
    {
        // Total number of booked seats for each session
        $dbConnection = $this->getEntityManager()->getConnection();
        $subSelect = $dbConnection->createQueryBuilder()
            ->select('movie_session_id', 'sum(1) as booked_seats')
            ->from('ticket')
            ->groupBy('movie_session_id')->getSQL();

        $today = new \DateTime();
        $q = $dbConnection->createQueryBuilder()
            ->select('movie_session_id, movie_session.startdate, movie_session.enddate, booked_seats, room.capacity, room.columns, (room.capacity-booked_seats) as seats_left, quality.name, quality.price')
            ->from('movie_session')
            ->join('movie_session', 'movie', 'ON movie_session.movie_id = movie.id')
            ->join('movie_session', 'room', 'ON movie_session.room_id = room.id')
            ->join('movie_session', 'quality', 'ON room.quality_id = quality.id')
            ->join('movie_session', 'theater', 'ON room.theater_id = theater.id')
            ->leftJoin('movie_session', sprintf('(%s)', $subSelect), 'bookings', 'movie_session.id = bookings.movie_session_id')
            ->andWhere('movie_session.id = :movieSessionId')
            ->setParameter('movieSessionId', $movieSessionId)
            ->orderBy('movie_session.startdate', 'ASC');

            return $q->executeQuery()->fetchAllAssociative()[0];
    }
}
