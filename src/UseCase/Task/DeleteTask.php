<?php

declare(strict_types=1);

namespace App\UseCase\Task;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteTask implements DeleteTaskInterface
{
    public function __construct(
        private readonly EntityManagerInterface $manager
    ) {
    }

    public function __invoke(Task $task): void
    {
        $this->manager->remove($task);
        $this->manager->flush();
    }
}
