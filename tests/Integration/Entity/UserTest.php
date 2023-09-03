<?php

declare(strict_types=1);

namespace App\Tests\Integration\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserTest extends KernelTestCase
{
    /**
     * @throws \Exception
     */
    public function testValidEntity(): void
    {
        $user = new User();
        $user->setUsername('user')->setEmail('user@test.fr');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($user);

        self::assertCount(0, $errors, $this->printErrors($errors));
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

        self::assertCount(1, $errors, $this->printErrors($errors));
    }

    /**
     * @throws \Exception
     */
    public function testInvalidUsernameRegex(): void
    {
        $user = new User();
        $user->setUsername('not_valid_regex')->setEmail('user@test.fr');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($user);

        self::assertCount(1, $errors, $this->printErrors($errors));
    }

    /**
     * @throws \Exception
     */
    public function testInvalidUsernameLength(): void
    {
        $user = new User();
        $user->setUsername('lo')->setEmail('user@test.fr');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($user);

        self::assertCount(1, $errors, $this->printErrors($errors));

        $user->setUsername('toolongusernamemustfailaaaabbbbccccdddd');
        $errors = self::getContainer()->get('validator')->validate($user);

        self::assertCount(1, $errors, $this->printErrors($errors));
    }

    private function printErrors(ConstraintViolationListInterface $errors): string
    {
        $messages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
        }

        return implode(', ', $messages);
    }
}
