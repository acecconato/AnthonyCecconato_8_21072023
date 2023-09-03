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
        if (null === $user->getRole() || 'ROLE_USER' === $user->getRole()) {
            $user->setRole('ROLE_ADMIN');
        } else {
            $user->setRole(null);
        }

        $this->manager->persist($user);
        $this->manager->flush();
    }
}
