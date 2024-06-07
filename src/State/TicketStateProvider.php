<?php

namespace App\State;

use App\Repository\TicketRepository;
use App\ApiResource\ApiTicket;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use Sqids\Sqids;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\CollectionOperationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TicketStateProvider implements ProviderInterface
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private Security $security,
    ) {
        Carbon::setLocale('fr');
    }

    /**
     * {@inheritDoc}
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        if ($user === null) {
            throw new AuthenticationException('Not authenticated or invalid token.');
        }
        if ($operation instanceof CollectionOperationInterface) {
            return $this->getListOfTickets($user->getId());
        }
        return $this->getOneTicket($uriVariables['ticketId']);
    }

    /**
     * Return the list of tickets for a given user id
     * that are linked to all future sessions or of the current day
     *
     * @param integer $userId
     * @return array
     */
    private function getListOfTickets(int $userId): array
    {
        $tickets = [];
        $dbTickets = $this->ticketRepository->findAllFutureSessionsOrOfTheDay($userId);

        foreach ($dbTickets as $dbTicket) {
            $ticket = new ApiTicket();
            // Encode the id to obfuscate it
            $sqids = new Sqids();
            $ticket->ticketId = $sqids->encode([$dbTicket->getId()]);
            $ticket->imdbId = $dbTicket->getMovieSession()->getMovie()->getImdbId();
            $ticket->movieTitle = $dbTicket->getMovieSession()->getMovie()->getTitle();
            $carbon = Carbon::instance($dbTicket->getMovieSession()->getStartdate());
            $ticket->day = $carbon->dayName;
            $ticket->roomNumber = $dbTicket->getMovieSession()->getRoom()->getNumber();
            $ticket->startDate = $dbTicket->getMovieSession()->getStartdate();
            $ticket->endDate = $dbTicket->getMovieSession()->getEnddate();
            $tickets[] = $ticket;
        }
        return $tickets;
    }

    /**
     * Return a ticket for a given user id and a given ticket id (obfuscated id)
     *
     * @param string $sqId
     * @return ApiTicket
     */
    private function getOneTicket(string $sqId): ApiTicket
    {
        // Decode the id to get the real id
        $sqids = new Sqids();
        $id = $sqids->decode($sqId)[0];

        $dbTicket = $this->ticketRepository->findTicketById($id);
        if (!$dbTicket) {
            throw new \Exception('Ticket <' . $sqId . '> not found');
        } else {
            $ticket = new ApiTicket();
            $ticket->ticketId = $sqId;
            $ticket->imdbId = $dbTicket->getMovieSession()->getMovie()->getImdbId();
            $ticket->movieTitle = $dbTicket->getMovieSession()->getMovie()->getTitle();
            $carbon = Carbon::instance($dbTicket->getMovieSession()->getStartdate());
            $ticket->day = $carbon->dayName;
            $ticket->roomNumber = $dbTicket->getMovieSession()->getRoom()->getNumber();
            $ticket->startDate = $dbTicket->getMovieSession()->getStartdate();
            $ticket->endDate = $dbTicket->getMovieSession()->getEnddate();
            $ticket->owner = $dbTicket->getOrdertickets()->getUser();
            $ticket->firstName = $ticket->owner->getFirstName();
            $ticket->lastName = $ticket->owner->getLastName();
            // Retrieve the number of people (tickets) in the order
            $ticket->nbTicketsInOrder = count($dbTicket->getOrdertickets()->getTickets());

            // Start the QR code engine
            $qrcodeEngine = new QRCode();
            // Serialize the ticket to JSON
            $encoders = [new XmlEncoder(), new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);
            // Generate the QR code
            $ticketSerialize = clone $ticket;
            unset($ticketSerialize->imdbId);
            unset($ticketSerialize->endDate);
            unset($ticketSerialize->owner);
            $data = $serializer->serialize($ticketSerialize, 'json');
            $qrcode = $qrcodeEngine->render($data);
            $ticket->qrCode = $qrcode;
            return $ticket;
        }
    }
}
