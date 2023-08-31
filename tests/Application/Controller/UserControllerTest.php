<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

use App\Doctrine\DataFixtures\UserFixtures;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private AbstractDatabaseTool $databaseTool;

    private KernelBrowser $client;

    private EntityManagerInterface $manager;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([UserFixtures::class]);
        $this->manager = self::getContainer()->get('doctrine.orm.entity_manager');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }

    public function testIndexSuccess(): void
    {
        $repo = $this->manager->getRepository(User::class);

        /** @var User $admin */
        $admin = $repo->findOneBy(['username' => 'admin']);

        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', '/comptes');
        self::assertResponseIsSuccessful();

        self::assertCount(10, $crawler->filter('table > tbody > tr'));
    }

    public function testIndexUnauthorized(): void
    {
        $this->client->request('GET', '/comptes');
        self::assertResponseStatusCodeSame(302);

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);

        $this->client->loginUser($user);
        self::assertResponseStatusCodeSame(302);
    }

    public function testCreateUser(): void
    {
        // Todo
    }

    public function testUpdateUser(): void
    {
        // Todo
    }

    public function testDeleteUser(): void
    {
        // Todo
    }

    public function testUpdateUserRole(): void
    {
        // Todo
    }
}
