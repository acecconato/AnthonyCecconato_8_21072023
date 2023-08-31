<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteUser implements DeleteUserInterface
{
    public function __construct(
        private readonly EntityManagerInterface $manager
    ) {
    }

    public function __invoke(User $user): void
    {
        $this->manager->remove($user);
        $this->manager->flush();
    }
}
