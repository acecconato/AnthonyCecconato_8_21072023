<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
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
}
