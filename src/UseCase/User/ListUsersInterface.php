<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Entity\User;

interface ListUsersInterface
{
    /**
     * @return User[]
     */
    public function __invoke(): array;

    /**
     * @return array{
     *     total_items: int,
     *     total_pages: int,
     *     items_per_page: int,
     *     page: int
     * }
     */
    public function getPaginationDatas(): array;
}
