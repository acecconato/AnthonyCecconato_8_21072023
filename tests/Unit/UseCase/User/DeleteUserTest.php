<?php

declare(strict_types=1);

namespace App\Tests\Unit\UseCase\User;

use App\Entity\User;
use App\UseCase\User\DeleteUser;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

final class DeleteUserTest extends TestCase
{
    public function testDeleteUserSuccess(): void
    {
        $entityManager = self::createMock(EntityManager::class);

        $user = new User();

        $entityManager->expects(self::once())->method('remove')->with($user);
        $entityManager->expects(self::once())->method('flush');

        $deleteUser = new DeleteUser($entityManager);
        $deleteUser($user);
    }
}
