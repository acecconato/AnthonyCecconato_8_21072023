<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;

final class ListUsers implements ListUsersInterface
{
    private int $page;

    public function __construct(RequestStack $request, private readonly UserRepository $repository, private readonly int $users_per_page)
    {
        $request = $request->getCurrentRequest();

        $this->page = (int) max($request?->query->get('page'), 1);
    }

    public function __invoke(): array
    {
        return $this->repository->getPaginatedUsersWithoutMe($this->page);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getPaginationDatas(): array
    {
        $total_items = $this->repository->countUsersWithoutMe();

        return [
            'total_items' => $total_items,
            'total_pages' => (int) ceil($total_items / $this->users_per_page),
            'items_per_page' => $this->users_per_page,
            'page' => $this->page,
        ];
    }
}
