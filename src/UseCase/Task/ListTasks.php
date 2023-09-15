<?php

declare(strict_types=1);

namespace App\UseCase\Task;

use App\DTO\ListTasksDTO;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\NonUniqueResultException;

final class ListTasks implements ListTasksInterface
{
    public function __construct(
        private readonly TaskRepository $repository
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(User $user, ListTasksDTO $listTasksDTO): array
    {
        $page = $listTasksDTO->getPage();
        $completed = $listTasksDTO->isCompleted();
        $anon = $listTasksDTO->isAnon();

        return match ($anon) {
            true => $this->repository->getAnonPaginatedTasks($page),
            false => $this->repository->getPaginatedTasks($user, $page, $completed),
        };
    }
}
