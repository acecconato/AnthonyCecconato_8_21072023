<?php

declare(strict_types=1);

namespace App\Tests\Unit\UseCase\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\UseCase\User\ListUsers;
use Doctrine\ORM\NonUniqueResultException;
use PHPUnit\Framework\TestCase;

final class ListUsersTest extends TestCase
{
    /**
     * @throws NonUniqueResultException
     */
    public function testListUsersSuccess(): void
    {
        $userRepository = self::createMock(UserRepository::class);

        $user = new User();

        $userRepository->expects(self::once())->method('getPaginatedUsersWithoutUser')->willReturn([
            'total_items' => 1,
            'total_pages' => 1,
            'items_per_page' => 1,
            'page' => 1,
            'embedded' => [$user],
        ]);

        $listUsers = new ListUsers($userRepository);
        $listUsers($user, 1);
    }
}
