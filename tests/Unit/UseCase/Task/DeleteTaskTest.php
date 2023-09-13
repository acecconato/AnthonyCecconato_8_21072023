<?php

declare(strict_types=1);

namespace App\Tests\Unit\UseCase\Task;

use App\Entity\Task;
use App\UseCase\Task\DeleteTask;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class DeleteTaskTest extends TestCase
{
    public function testDeleteTaskSuccess(): void
    {
        $entityManager = self::createMock(EntityManagerInterface::class);

        $task = new Task();

        $entityManager->expects(self::once())->method('remove')->with($task);
        $entityManager->expects(self::once())->method('flush');

        $deleteTask = new DeleteTask($entityManager);
        $deleteTask($task);
    }
}
