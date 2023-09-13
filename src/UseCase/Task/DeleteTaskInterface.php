<?php

declare(strict_types=1);

namespace App\UseCase\Task;

use App\Entity\Task;

interface DeleteTaskInterface
{
    public function __invoke(Task $task): void;
}
