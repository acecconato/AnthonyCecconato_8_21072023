<?php

declare(strict_types=1);

namespace App\UseCase\Task;

use App\Repository\TaskRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;

class ListTasks implements ListTasksInterface
{
    public function __construct(
        private readonly TaskRepository $repository,
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(bool $completed = false, bool $anon = false): array
    {
        $page = (int) $this->requestStack->getCurrentRequest()?->query->getInt('page', 1);
        $completed = (bool) $this->requestStack->getCurrentRequest()?->query->getBoolean('completed');
        $anon = (bool) $this->requestStack->getCurrentRequest()?->query->getBoolean('anon');

        return $this->repository->getPaginatedTasks($page, $completed, $anon);
    }
}
