<?php
namespace App\Service;

use App\Entity\MovieSession;
use BadFunctionCallException;
use Exception;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use MongoDB\Driver\ServerApi;
use Psr\Log\LoggerInterface;

class MongoDbService
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Test if the MongoDB service is available
     * Return an array with the status and the message as key and value
     *
     * @return array associative array with the status and the message
     */
    public function isAvailable(): array
    {
        if (!extension_loaded('mongodb')) {
            $this->logger->error('MongoDbService::isAvailable: The MongoDB extension is not enabled');
            return ['status' => false, 'message' => 'The MongoDB extension is not enabled'];
        }
        if (!class_exists('\MongoDB\Client')) {
            $this->logger->error('MongoDbService::isAvailable: The MongoDB PHP library is not installed');
            return ['status' => false, 'message' => 'The MongoDB PHP library is not installed'];
        }
        if (empty($_ENV['MONGODB_DSN'])) {
            $this->logger->error('MongoDbService::isAvailable: The MONGODB_DSN env variable is not defined');
            return ['status' => false, 'message' => 'The MONGODB_DSN env variable is not defined'];
        }
        if (empty($_ENV['MONGODB_DB'])) {
            $this->logger->error('MongoDbService::isAvailable: The MONGODB_DB env variable is not defined');
            return ['status' => false, 'message' => 'The MONGODB_DB env variable is not defined'];
        }
        if (empty($_ENV['MONGODB_COLLECTION'])) {
            $this->logger->error('MongoDbService::isAvailable: The MONGODB_COLLECTION env variable is not defined');
            return ['status' => false, 'message' => 'The MONGODB_COLLECTION env variable is not defined'];
        }

        try {
            // Send a ping to confirm a successful connection
            $uri = $_ENV['MONGODB_DSN'];
            $apiVersion = new ServerApi(ServerApi::V1);
            $client = new Client($uri, [], ['serverApi' => $apiVersion]);
            $client->selectDatabase($_ENV['MONGODB_DB'])->command(['ping' => 1]);
            $this->logger->debug('MongoDbService::isAvailable: The MongoDB service was pinged successfully');
            return ['status' => true, 'message' => 'The MongoDB service is available'];
        } catch (Exception $e) {
            $this->logger->error('MongoDbService::isAvailable failed: ' . $e->getMessage());
            return ['status' => false, 'message' => 'The MongoDB service is not available'];
        }
    }

    /**
     * Store the booking in MongoDB for later analysis
     *
     * @param MovieSession $session Movie session
     * @param array $seatIds list of booked seat ids
     * @return void
     */
    public function storeBooking(MovieSession $session, array $seatIds): void
    {
        // Create a new MongoDB client and store the booking for later analysis
        try {
            $date = new UTCDateTime();
            $uri = $_ENV['MONGODB_DSN'];
            $apiVersion = new ServerApi(ServerApi::V1);
            $client = new Client($uri, [], ['serverApi' => $apiVersion]);
            $collection = $client->selectDatabase($_ENV['MONGODB_DB'])->selectCollection($_ENV['MONGODB_COLLECTION']);
            $insertOneResult = $collection->insertOne([
                'movieTitle' => $session->getMovie()->getTitle(),
                'tickets' => count($seatIds),
                'timestamp' => $date
            ]);
            $this->logger->debug('MongoDbService::storeBooking: The booking was stored in MongoDB with the id ' . $insertOneResult->getInsertedId());
        } catch (Exception $e) {
            $this->logger->error('MongoDbService::storeBooking failed: ' . $e->getMessage());
        }
    }

    /**
     * Query the bookings from MongoDB
     * Orders for the last 7 days ordered by movie title
     * @return array
     */
    public function queryBookings(): array
    {
        // Create a new MongoDB client and store the booking for later analysis
        try {
            $date = new UTCDateTime(new \DateTime('7 days ago'));
            $uri = $_ENV['MONGODB_DSN'];
            $apiVersion = new ServerApi(ServerApi::V1);
            $client = new Client($uri, [], ['serverApi' => $apiVersion]);
            $collection = $client->selectDatabase($_ENV['MONGODB_DB'])->selectCollection($_ENV['MONGODB_COLLECTION']);
            $cursor = $collection->aggregate([
                [
                    '$match'=> [
                        'timestamp'=> [
                            '$gte'=> $date
                        ]
                    ]
                ],
                [
                    '$group'=> [
                        '_id'=> '$movieTitle',
                        'count'=> [
                            '$sum'=> '$tickets'
                        ]
                    ]
                ],
                [
                    '$sort'=> [
                        'count'=> -1
                    ]
                ]
            ]);
            $bookings = [];
            foreach ($cursor as $document) {
                $bookings[$document['_id']] = $document['count'];
            }
            $this->logger->debug('MongoDbService::queryBookings: The bookings were retrieved from MongoDB');
            return $bookings;
        } catch (Exception $e) {
            $this->logger->error('MongoDbService::queryBookings failed: ' . $e->getMessage());
            return [];
        }
    }
}