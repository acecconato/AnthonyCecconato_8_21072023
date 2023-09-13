<?php

declare(strict_types=1);

namespace App\Tests\Unit\UseCase\Task;

use App\Entity\Task;
use App\UseCase\Task\MarkTask;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class MarkTaskTest extends TestCase
{
    public function testMarkTaskSuccess(): void
    {
        $entityManager = self::createMock(EntityManagerInterface::class);

        $task = new Task();

        $entityManager->expects(self::once())->method('persist')->with($task);
        $entityManager->expects(self::once())->method('flush');

        $markTask = new MarkTask($entityManager);
        $markTask($task);

        self::assertTrue($task->isCompleted());
    }
}
