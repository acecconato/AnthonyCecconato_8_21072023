<?php

declare(strict_types=1);

namespace App\UseCase\Task;

use App\Entity\Task;

interface ListTasksInterface
{
    /**
     * @return array{
     *     total_items: int,
     *     total_pages: int,
     *     items_per_page: int,
     *     page: int,
     *     embedded: Task[]
     * }
     */
    public function __invoke(): array;
}
