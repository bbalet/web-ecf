<?php

namespace App\State;

use App\ApiResource\ApiUser;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserStateProvider implements ProviderInterface
{
    public function __construct(
        private Security $security
    ) {}

    /**
     * {@inheritDoc}
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User */
        $user = $this->security->getUser();
        if ($user === null) {
            throw new AuthenticationException('Not authenticated or invalid token.');
        } else {
            $apiUser = new ApiUser();
            $apiUser->userId = $user->getId();
            $apiUser->firstName = $user->getFirstName();
            $apiUser->lastName = $user->getLastName();
            $apiUser->role = $user->getRoles()[0];
            return $apiUser;
        }
    }
}
