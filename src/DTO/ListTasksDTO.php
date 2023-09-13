<?php

declare(strict_types=1);

namespace App\DTO;

final class ListTasksDTO
{
    public function __construct(
        private int $page = 1,
        private bool $completed = false,
        private bool $anon = false,
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): ListTasksDTO
    {
        $this->page = $page;

        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): ListTasksDTO
    {
        $this->completed = $completed;

        return $this;
    }

    public function isAnon(): bool
    {
        return $this->anon;
    }

    public function setAnon(bool $anon): ListTasksDTO
    {
        $this->anon = $anon;

        return $this;
    }
}
