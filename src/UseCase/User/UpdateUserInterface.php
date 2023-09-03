<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Entity\User;

interface UpdateUserInterface
{
    public function __invoke(User $user, string $plainPassword = null): void;
}
