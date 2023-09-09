<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;

final class ListUsers implements ListUsersInterface
{
    private int $page;

    public function __construct(RequestStack $request, private readonly UserRepository $repository, private readonly int $usersPerPage)
    {
        $request = $request->getCurrentRequest();

        $this->page = (int) max($request?->query->get('page'), 1);
    }

    public function __invoke(User $user): array
    {
        return $this->repository->getPaginatedUsersWithoutMe($user, $this->page);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getPaginationDatas(): array
    {
        $total_items = $this->repository->countUsersWithoutMe();

        return [
            'total_items' => $total_items,
            'total_pages' => (int) ceil($total_items / $this->usersPerPage),
            'items_per_page' => $this->usersPerPage,
            'page' => $this->page,
        ];
    }
}
