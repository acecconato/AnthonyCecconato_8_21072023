<?php

declare(strict_types=1);

namespace App\UseCase\Task;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class CreateTask implements CreateTaskInterface
{
    public function __construct(
        private readonly EntityManagerInterface $manager
    ) {
    }

    public function __invoke(Task $task, User $user): void
    {
        $task->setOwner($user);

        $this->manager->persist($task);
        $this->manager->flush();
    }
}
