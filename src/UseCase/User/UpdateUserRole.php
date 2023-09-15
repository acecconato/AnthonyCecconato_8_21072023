<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class UpdateUserRole implements UpdateUserRoleInterface
{
    public function __construct(
        private readonly EntityManagerInterface $manager
    ) {
    }

    public function __invoke(User $user): void
    {
        match ($user->getRole()) {
            'ROLE_ADMIN' => $user->setRole('ROLE_USER'),
            default => $user->setRole('ROLE_ADMIN'),
        };

        $this->manager->persist($user);
        $this->manager->flush();
    }
}
