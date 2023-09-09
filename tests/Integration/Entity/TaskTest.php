<?php

declare(strict_types=1);

namespace App\Tests\Integration\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class TaskTest extends AbstractEntityTest
{
    /**
     * @throws \Exception
     */
    public function testValidEntity(): void
    {
        $task = new Task();
        $task->setTitle('title')->setContent('content')->setCompleted(true);

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($task);

        self::assertCount(0, $errors, $this->printErrors($errors));
    }

    /**
     * @throws \Exception
     */
    public function testValidEntityWithoutContent(): void
    {
        $task = new Task();
        $task->setTitle('title');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($task);

        self::assertCount(0, $errors, $this->printErrors($errors));
    }

    /**
     * @throws \Exception
     */
    public function testOwnerValidEntity(): void
    {
        self::bootKernel();
        $owner = self::getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->findOneBy(
            ['username' => 'demo']
        );

        $task = new Task();
        $task->setTitle('title')->setContent('content');
        $task->setOwner($owner);

        $errors = self::getContainer()->get('validator')->validate($task);
        self::assertCount(0, $errors, $this->printErrors($errors));
    }

    /**
     * @throws \Exception
     */
    public function testConstraintNotBlankTitle(): void
    {
        $task = new Task();
        $task->setTitle('')->setContent('content');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($task);

        self::assertTrue($this->hasConstraint(NotBlank::class, 'title', $errors), $this->printErrors($errors));
    }

    /**
     * @throws \Exception
     */
    public function testConstraintMinLengthTitle(): void
    {
        $task = new Task();
        $task->setTitle('aa')->setContent('content');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($task);

        self::assertTrue($this->hasConstraint(Length::class, 'title', $errors), $this->printErrors($errors));
    }

    /**
     * @throws \Exception
     */
    public function testConstraintMaxLengthTitle(): void
    {
        $task = new Task();

        while (strlen($task->getTitle() ?? '') <= 255) {
            $task->setTitle($task->getTitle().'abcde');
        }

        $task->setContent('content');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($task);

        self::assertTrue($this->hasConstraint(Length::class, 'title', $errors), $this->printErrors($errors));
    }

    /**
     * @throws \Exception
     */
    public function testConstraintMaxLengthContent(): void
    {
        $task = new Task();

        while (strlen($task->getContent() ?? '') <= 1000) {
            $task->setContent($task->getContent().'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz');
        }

        $task->setTitle('title');

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($task);

        self::assertTrue($this->hasConstraint(Length::class, 'content', $errors), $this->printErrors($errors));
    }
}
