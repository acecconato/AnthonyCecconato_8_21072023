<?php

declare(strict_types=1);

namespace App\Tests\Integration\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractEntityTest extends KernelTestCase
{
    protected function hasConstraint(string $constraint, string $propertyPath, ConstraintViolationListInterface $errors): bool
    {
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            if ($error->getConstraint() instanceof $constraint && $error->getPropertyPath() === $propertyPath) {
                return true;
            }
        }

        return false;
    }

    protected function printErrors(ConstraintViolationListInterface $errors): string
    {
        $messages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
        }

        return implode(', ', $messages);
    }
}
