<?php

namespace App\State;

use App\Repository\TheaterRepository;
use App\ApiResource\ApiTheater;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

class TheaterStateProvider implements ProviderInterface
{
    public function __construct(
        private TheaterRepository $theaterRepository
    ) {}

    /**
     * {@inheritDoc}
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->getListOfTheaters();
    }

    /**
     * Return the list of theaters
     *
     * @return array list of ApiTheater objects
     */
    private function getListOfTheaters(): array
    {
        $theaters = [];
        $dbTheaters = $this->theaterRepository->findAll();

        foreach ($dbTheaters as $dbTheater) {
            $theater = new ApiTheater();
            $theater->id = $dbTheater->getId();
            $theater->city = $dbTheater->getCity();
            $theaters[] = $theater;
        }
        return $theaters;
    }
}
