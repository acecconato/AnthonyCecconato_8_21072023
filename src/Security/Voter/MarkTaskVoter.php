<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class MarkTaskVoter extends Voter
{
    public const MARK = 'mark';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (self::MARK != $attribute) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User || null === $user->getId()) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        return $task->getOwner() === $user || null === $task->getOwner();
    }
}
