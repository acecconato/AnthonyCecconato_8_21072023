<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;

final class ListUsers implements ListUsersInterface
{
    public function __construct(
        private readonly UserRepository $repository
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(User $user, int $page): array
    {
        return $this->repository->getPaginatedUsersWithoutUser($user, $page);
    }
}
