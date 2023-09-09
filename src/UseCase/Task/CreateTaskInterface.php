<?php

declare(strict_types=1);

namespace App\UseCase\Task;

use App\Entity\Task;
use App\Entity\User;

interface CreateTaskInterface
{
    public function __invoke(Task $task, User $user): void;
}
