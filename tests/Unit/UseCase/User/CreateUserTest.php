<?php

declare(strict_types=1);

namespace App\Tests\Unit\UseCase\User;

use App\Entity\User;
use App\UseCase\User\CreateUser;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

final class CreateUserTest extends TestCase
{
    public function testCreateUserSuccess(): void
    {
        $hasher = self::createMock(UserPasswordHasher::class);
        $entityManager = self::createMock(EntityManager::class);

        $user = new User();

        $hasher->expects(self::once())->method('hashPassword');
        $entityManager->expects(self::once())->method('persist')->with($user);
        $entityManager->expects(self::once())->method('flush');

        $createUser = new CreateUser($hasher, $entityManager);
        $createUser($user, 'password');
    }
}
