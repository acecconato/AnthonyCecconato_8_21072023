<?php

declare(strict_types=1);

namespace App\Tests\Integration\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

final class UserTest extends AbstractEntityTest
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

        self::assertTrue($this->hasConstraint(Email::class, 'email', $errors), $this->printErrors($errors));
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

        self::assertTrue($this->hasConstraint(Regex::class, 'username', $errors), $this->printErrors($errors));
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

        self::assertTrue($this->hasConstraint(Length::class, 'username', $errors), $this->printErrors($errors));
    }

    /**
     * @throws \Exception
     */
    public function testOnDeleteCascadeTasks(): void
    {
        self::bootKernel();
        $manager = self::getContainer()->get('doctrine.orm.entity_manager');

        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['username' => 'demo']);
        $ownerId = $user->getId();

        self::assertCount(20, $user->getTasks());

        $manager->remove($user);
        $manager->flush();

        self::assertNull($manager->getRepository(User::class)->findOneBy(['username' => 'demo']));

        self::assertCount(0, $manager->getRepository(Task::class)->findBy(['owner' => $ownerId]));
    }

    /**
     * @throws \Exception
     */
    public function testUserAddTask(): void
    {
        self::bootKernel();
        $manager = self::getContainer()->get('doctrine.orm.entity_manager');

        $task = new Task();
        $task->setTitle('title')->setContent('title');

        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['username' => 'demo']);
        $user->addTask($task);

        $manager->persist($user);
        $manager->persist($task);
        $manager->flush();

        self::assertNotNull($task->getId());
        self::assertEquals($user, $task->getOwner());
    }

    /**
     * @throws \Exception
     */
    public function testUserRemoveTask(): void
    {
        self::bootKernel();
        $manager = self::getContainer()->get('doctrine.orm.entity_manager');

        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['username' => 'demo']);

        /** @var Task[] $tasks */
        $tasks = $user->getTasks();

        self::assertCount(20, $tasks);

        $taskToRemove = $tasks[0];

        $user->removeTask($taskToRemove);

        $manager->persist($user);
        $manager->flush();

        self::assertCount(19, $user->getTasks());
        self::assertNotNull($user->getId());
        self::assertNull($taskToRemove->getOwner());
    }
}
