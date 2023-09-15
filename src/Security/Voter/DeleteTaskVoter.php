<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class DeleteTaskVoter extends Voter
{
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (self::DELETE != $attribute) {
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

        return $this->isTaskOwner($task, $user) || $this->canRemoveAnonTask($task, $user);
    }

    private function isTaskOwner(Task $task, User $user): bool
    {
        return $task->getOwner() === $user;
    }

    private function canRemoveAnonTask(Task $task, User $user): bool
    {
        return null === $task->getOwner() && 'ROLE_ADMIN' === $user->getRole();
    }
}
