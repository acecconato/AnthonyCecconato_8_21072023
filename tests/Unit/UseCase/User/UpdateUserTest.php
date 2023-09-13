<?php

declare(strict_types=1);

namespace App\Tests\Unit\UseCase\User;

use App\Entity\User;
use App\UseCase\User\UpdateUser;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

final class UpdateUserTest extends TestCase
{
    public function testUpdateUserSuccess(): void
    {
        $hasher = self::createMock(UserPasswordHasher::class);
        $entityManager = self::createMock(EntityManager::class);

        $user = new User();

        $hasher->expects(self::once())->method('hashPassword')->willReturn('password');

        $entityManager->expects(self::once())->method('persist')->with($user);
        $entityManager->expects(self::once())->method('flush');

        $updateUser = new UpdateUser($hasher, $entityManager);
        $updateUser($user, 'password');

        self::assertSame('password', $user->getPassword());
    }
}
