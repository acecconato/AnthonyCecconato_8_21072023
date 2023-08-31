<?php

declare(strict_types=1);

namespace App\UseCase\User;

use Symfony\Component\Form\FormInterface;

interface UpdateUserInterface
{
    public function __invoke(FormInterface $form): void;
}
