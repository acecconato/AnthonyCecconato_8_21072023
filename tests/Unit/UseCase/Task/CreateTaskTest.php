<?php

declare(strict_types=1);

namespace App\Tests\Unit\UseCase\Task;

use App\Entity\Task;
use App\Entity\User;
use App\UseCase\Task\CreateTask;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateTaskTest extends TestCase
{
    public function testCreateTaskSuccess(): void
    {
        $entityManager = self::createMock(EntityManagerInterface::class);

        $security = self::createMock(Security::class);

        $user = new User();
        $security->expects(self::once())->method('getUser')->willReturn($user);

        $task = new Task();
        $entityManager->expects(self::once())->method('persist')->with($task);
        $entityManager->expects(self::once())->method('flush');

        $createTask = new CreateTask($entityManager, $security);
        $createTask($task);

        self::assertSame($user, $task->getOwner());
    }
}
