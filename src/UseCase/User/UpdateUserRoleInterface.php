<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Entity\User;

interface UpdateUserRoleInterface
{
    public function __invoke(User $user): void;
}
