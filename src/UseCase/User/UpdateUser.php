<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UpdateUser implements UpdateUserInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $manager
    ) {
    }

    public function __invoke(User $user, string $plainPassword = null): void
    {
        if (null !== $plainPassword) {
            $hashedPassword = $this->hasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        $this->manager->persist($user);
        $this->manager->flush();
    }
}
