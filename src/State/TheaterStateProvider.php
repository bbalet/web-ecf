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
        return $this->getListOfTheaters($context);
    }

    /**
     * Return the list of theaters optionally filtered by latitude and longitude
     *
     * @param mixed $context can be used to pass filters on lat/long
     * @return array list of ApiTheater objects
     */
    private function getListOfTheaters($context): array
    {
        $theaters = [];
        $dbTheaters = null;
        if (isset($context['filters']['latitude']) and isset($context['filters']['longitude'])) {
            if (($context['filters']['latitude'] != '') and ($context['filters']['longitude'] != '')) {
                $dbTheaters = $this->theaterRepository->findNearestTheaters(
                    $context['filters']['latitude'],
                    $context['filters']['longitude']
                );
                foreach ($dbTheaters as $dbTheater) {
                    $theater = new ApiTheater();
                    $theater->id = $dbTheater['id'];
                    $theater->city = $dbTheater['city'];
                    $theater->latitude = $dbTheater['latitude'];
                    $theater->longitude = $dbTheater['longitude'];
                    $theaters[] = $theater;
                }
                return $theaters;
            } else {
                $dbTheaters = $this->theaterRepository->findAll();
            }
        } else {
            $dbTheaters = $this->theaterRepository->findAll();
        }

        foreach ($dbTheaters as $dbTheater) {
            $theater = new ApiTheater();
            $theater->id = $dbTheater->getId();
            $theater->city = $dbTheater->getCity();
            $theater->latitude = $dbTheater->getLatitude();
            $theater->longitude = $dbTheater->getLongitude();
            $theaters[] = $theater;
        }
        return $theaters;
    }
}
