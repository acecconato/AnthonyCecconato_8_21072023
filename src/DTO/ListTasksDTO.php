<?php

declare(strict_types=1);

namespace App\DTO;

final class ListTasksDTO
{
    public function __construct(
        private readonly int $page,
        private readonly bool $completed,
        private readonly bool $anon,
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function isAnon(): bool
    {
        return $this->anon;
    }
}
