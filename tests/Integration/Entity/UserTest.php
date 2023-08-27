<?php

declare(strict_types=1);

namespace App\Tests\Integration\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    /**
     * @throws \Exception
     */
    public function testValidEntity(): void
    {
        $user = new User();
        $user->setUsername('user')->setEmail('demo@demo.fr');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($user);

        self::assertCount(0, $errors);
    }

    /**
     * @throws \Exception
     */
    public function testInvalidEmail(): void
    {
        $user = new User();
        $user->setUsername('user')->setEmail('not_an_email');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($user);

        self::assertCount(1, $errors);
    }

    /**
     * @throws \Exception
     */
    public function testInvalidUsernameRegex(): void
    {
        $user = new User();
        $user->setUsername('not_valid_regex')->setEmail('demo@demo.fr');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($user);

        self::assertCount(1, $errors);
    }

    /**
     * @throws \Exception
     */
    public function testInvalidUsernameLength(): void
    {
        $user = new User();
        $user->setUsername('lo')->setEmail('demo@demo.fr');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($user);

        self::assertCount(1, $errors);

        $user->setUsername('toolongusernamemustfailaaaabbbbccccdddd');
        $errors = self::getContainer()->get('validator')->validate($user);

        self::assertCount(1, $errors);
    }
}
