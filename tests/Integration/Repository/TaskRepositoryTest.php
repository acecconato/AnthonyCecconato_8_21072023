<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $manager;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->manager = self::getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedTasks(): void
    {
        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);

        $tokenStorage = self::getContainer()->get('security.token_storage');
        $token = new UsernamePasswordToken($user, 'main', ['ROLE_USER']);
        $tokenStorage->setToken($token);

        $tasks = $this->manager->getRepository(Task::class)->getPaginatedTasks($user, page: 1, completed: true);

        self::assertEquals(10, $tasks['total_items']);
        self::assertEquals(1, $tasks['page']);
        self::assertEquals(9, $tasks['items_per_page']);
        self::assertEquals(2, $tasks['total_pages']);
        self::assertCount(9, $tasks['embedded']);
        self::assertContainsOnlyInstancesOf(Task::class, $tasks['embedded']);
    }

    /**
     * @throws \Exception
     */
    public function testGetAnonPaginatedTasks(): void
    {
        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);

        $tokenStorage = self::getContainer()->get('security.token_storage');
        $token = new UsernamePasswordToken($user, 'main', ['ROLE_USER']);
        $tokenStorage->setToken($token);

        $tasks = $this->manager->getRepository(Task::class)->getAnonPaginatedTasks(page: 1);

        self::assertEquals(20, $tasks['total_items']);
        self::assertEquals(1, $tasks['page']);
        self::assertEquals(9, $tasks['items_per_page']);
        self::assertEquals(3, $tasks['total_pages']);
        self::assertCount(9, $tasks['embedded']);
        self::assertContainsOnlyInstancesOf(Task::class, $tasks['embedded']);
    }
}
