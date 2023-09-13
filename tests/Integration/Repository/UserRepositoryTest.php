<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\InMemoryUser;

class UserRepositoryTest extends KernelTestCase
{
    /**
     * @throws \Exception
     */
    public function testUpgradePassword(): void
    {
        self::bootKernel();

        /** @var UserRepository $repo */
        $repo = self::getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);

        /** @var User $user */
        $user = $repo->findOneBy(['username' => 'demo']);
        $oldPassword = $user->getPassword();

        $hasher = self::getContainer()->get('security.user_password_hasher');

        $repo->upgradePassword($user, $hasher->hashPassword($user, 'demo'));

        self::assertNotEquals($repo->find($user->getId())?->getPassword(), $oldPassword);
    }

    /**
     * @throws \Exception
     */
    public function testUpgradePasswordUnsupportedUserException(): void
    {
        self::bootKernel();

        /** @var UserRepository $repo */
        $repo = self::getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);

        $user = new InMemoryUser('username', 'password');

        self::expectException(UnsupportedUserException::class);
        /* @phpstan-ignore-next-line */
        $repo->upgradePassword($user, 'password');
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedUsersWithoutUser(): void
    {
        $manager = self::getContainer()->get('doctrine.orm.entity_manager');

        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['username' => 'admin']);

        $tokenStorage = self::getContainer()->get('security.token_storage');
        $token = new UsernamePasswordToken($user, 'main', ['ROLE_ADMIN']);
        $tokenStorage->setToken($token);

        /** @var UserRepository $repo */
        $repo = $manager->getRepository(User::class);
        $users = $repo->getPaginatedUsersWithoutUser($user, 1);

        self::assertEquals(11, $users['total_items']);
        self::assertEquals(1, $users['page']);
        self::assertEquals(9, $users['items_per_page']);
        self::assertEquals(2, $users['total_pages']);
        self::assertCount(9, $users['embedded']);
        self::assertContainsOnlyInstancesOf(User::class, $users['embedded']);
    }
}
