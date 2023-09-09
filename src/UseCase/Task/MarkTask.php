<?php

declare(strict_types=1);

namespace App\UseCase\Task;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

final class MarkTask implements MarkTaskInterface
{
    public function __construct(
        private readonly EntityManagerInterface $manager
    ) {
    }

    public function __invoke(Task $task): void
    {
        $task->setCompleted(!$task->isCompleted());

        $this->manager->persist($task);
        $this->manager->flush();
    }
}
