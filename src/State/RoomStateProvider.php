<?php

namespace App\State;

use App\Repository\TheaterRepository;
use App\ApiResource\ApiRoom;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

class RoomStateProvider implements ProviderInterface
{
    public function __construct(
        private TheaterRepository $theaterRepository
    ) {}

    /**
     * {@inheritDoc}
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->getListOfRoomsInATheater($context['filters']['theaterId']);
    }

    /**
     * Return the list of the rooms in a theater
     *
     * @return array list of ApiRoom objects
     */
    private function getListOfRoomsInATheater($theaterId): array
    {
        $rooms = [];
        $theater = $this->theaterRepository->findOneBy(['id' => $theaterId]);
        if (!$theater) {
            throw new \Exception('Theater #' .$theaterId . ' not found');
        }

        $dbRooms = $this->theaterRepository->findOneBy(['id' => $theaterId])->getRooms();

        foreach ($dbRooms as $dbRoom) {
            $room = new ApiRoom();
            $room->roomId = $dbRoom->getId();
            $room->number = $dbRoom->getNumber();
            $rooms[] = $room;
        }
        return $rooms;
    }
}
