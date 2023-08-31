<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

    public function testAppSuccess(): void
    {
        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);
        $this->client->loginUser($user);
        $this->client->request('GET', '/app');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Dashboard');
    }

    public function testAppUnauthorized(): void
    {
        $this->client->request('GET', '/app');

        self::assertResponseStatusCodeSame(302);

        $this->client->followRedirect();

        self::assertSelectorTextContains('h1', 'Connexion');
    }
}
