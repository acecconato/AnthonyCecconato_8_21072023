<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Entity\User;

interface ListUsersInterface
{
    /**
     * @return array{
     *     total_items: int,
     *     total_pages: int,
     *     items_per_page: int,
     *     page: int,
     *     embedded: User[]
     * }
     */
    public function __invoke(User $user, int $page): array;
}
