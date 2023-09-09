<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $manager;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->manager = self::getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testIndexListTodoTasksSuccess(): void
    {
        $this->login();

        $this->client->request('GET', '/app');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Bienvenue sur Todo List');

        self::assertSelectorExists('a#task-add');
        self::assertSelectorExists('a#completed-tasks');
        self::assertSelectorExists('a#anon-tasks');
        self::assertSelectorCount(9, '.task-list .task');
    }

    public function testIndexListCompletedTasksSuccess(): void
    {
        $this->login();

        $this->client->request('GET', '/app', ['completed' => true]);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Bienvenue sur Todo List');

        self::assertSelectorExists('a#task-add');
        self::assertSelectorExists('a#completed-tasks');
        self::assertSelectorExists('a#anon-tasks');
        self::assertSelectorCount(9, '.task-list .task');
    }

    public function testIndexAnonTasksSuccess(): void
    {
        $this->login();

        $this->client->request('GET', '/app', ['anon' => true]);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Bienvenue sur Todo List');

        self::assertSelectorExists('a#task-add');
        self::assertSelectorExists('a#completed-tasks');
        self::assertSelectorExists('a#anon-tasks');
        self::assertSelectorCount(9, '.task-list .task');
    }

    public function testIndexAccessDeniedRedirects(): void
    {
        $this->client->request('GET', '/app');

        self::assertResponseStatusCodeSame(302);

        $this->client->followRedirect();

        self::assertSelectorTextContains('h1', 'Connexion');
    }

    public function testCreateTaskSuccess(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/app/taches/creer');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Créer une nouvelle tâche');

        $form = $crawler->selectButton('submit')->form();
        $form->setValues([
            'task[title]' => 'Test',
            'task[content]' => 'Test content',
        ]);

        $this->client->submit($form);

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        /** @var Task $createdTask */
        $createdTask = $this->manager->getRepository(Task::class)->findOneBy(['title' => 'Test']);
        self::assertNotNull($createdTask->getId());
    }

    public function testCreateTaskFormErrors(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/app/taches/creer');

        $form = $crawler->selectButton('submit')->form();

        $content = '';
        while (strlen($content) <= 1000) {
            $content .= 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz';
        }

        $form->setValues([
            'task[title]' => 'ab',
            'task[content]' => $content,
        ]);

        $this->client->submit($form);

        self::assertSelectorCount(2, '.invalid-feedback');
    }

    public function testCreateTaskAccessDeniedRedirects(): void
    {
        $this->client->request('GET', '/app/taches/creer');

        self::assertResponseStatusCodeSame(302);

        $this->client->followRedirect();

        self::assertSelectorTextContains('h1', 'Connexion');
    }

//    /**
//     * @throws \Exception
//     */
//    public function testMarkTaskAccessDeniedException(): void
//    {
//        $this->login();
//
//        /** @var Task $task */
//        $task = self::getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class)->findAll()[0];
//
//        $this->client->catchExceptions(false);
//        self::expectException(AccessDeniedException::class);
//
//        $this->client->request('GET', 'app/taches/'.$task->getId().'/marquer');
//    }

    /**
     * @throws \Exception
     */
    public function testMarkTaskNotOwnerAccessDeniedException(): void
    {
        self::bootKernel();

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);

        /** @var User $user2 */
        $user2 = $this->manager->getRepository(User::class)->findOneBy(['username' => 'user1']);

        /** @var Task $task */
        $task = $user2->getTasks()[0];

        $this->client->catchExceptions(false);
        self::expectException(AccessDeniedException::class);

        $this->client->request('GET', 'app/taches/'.$task->getId().'/marquer');
    }

    /**
     * @throws \Exception
     */
    public function testMarkTaskSuccess(): void
    {
        $user = $this->login();

        /** @var Task $task */
        $task = array_filter($user->getTasks()->toArray(), fn ($task) => $task->isCompleted())[0];

        $this->client->request('GET', 'app/taches/'.$task->getId().'/marquer');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        /** @var Task $task */
        $task = self::getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class)->findOneBy(['id' => $task->getId()]);

        self::assertEquals(false, $task->isCompleted());
    }

    /**
     * @throws \Exception
     */
    public function testDeleteTaskSuccess(): void
    {
        $user = $this->login();

        /** @var Task $task */
        $task = $user->getTasks()[0];

        $this->client->request('GET', 'app/taches/'.$task->getId().'/supprimer');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertNull($task->getId());
    }

    /**
     * @throws \Exception
     */
    public function testDeleteTaskAccessDeniedException(): void
    {
        self::bootKernel();

        /** @var Task $task */
        $task = self::getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class)->findAll()[0];

        $this->client->catchExceptions(false);
        self::expectException(AccessDeniedException::class);

        $this->client->request('GET', 'app/taches/'.$task->getId().'/supprimer');
    }

    /**
     * @throws \Exception
     */
    public function testDeleteTaskNotOwnerAccessDeniedException(): void
    {
        $this->login();

        /** @var User $user2 */
        $user2 = $this->manager->getRepository(User::class)->findOneBy(['username' => 'user1']);

        /** @var Task $task */
        $task = $user2->getTasks()[0];

        $this->client->catchExceptions(false);
        self::expectException(AccessDeniedException::class);

        $this->client->request('GET', 'app/taches/'.$task->getId().'/supprimer');
    }

    private function login(): User
    {
        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);
        $this->client->loginUser($user);

        return $user;
    }
}
