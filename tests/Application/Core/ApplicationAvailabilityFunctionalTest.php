<?php

declare(strict_types=1);

namespace App\Tests\Application\Core;

use App\Doctrine\DataFixtures\UserFixtures;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    protected AbstractDatabaseTool $databaseTool;

    protected KernelBrowser $client;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }

    private function getAdmin(): ?User
    {
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        return $em->getRepository(User::class)->findOneBy(['username' => 'admin']);
    }

    private function getUser(): ?User
    {
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        return $em->getRepository(User::class)->findOneBy(['username' => 'demo']);
    }

    /**
     * @dataProvider provideNoAuthUrls
     */
    public function testPageIsSuccessful(string $url): void
    {
        $this->client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }

    /**
     * @dataProvider provideRoleUserUrls
     */
    public function testRoleUserPageIsSuccessful(string $url): void
    {
        $this->databaseTool->loadFixtures([UserFixtures::class]);

        /** @var UserInterface $user */
        $user = $this->getUser();

        $this->client->loginUser($user);

        $this->client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    /**
     * @dataProvider provideRoleAdminUrls
     */
    public function testRoleAdminPageIsSuccessful(string $url): void
    {
        $this->databaseTool->loadFixtures([UserFixtures::class]);

        /** @var UserInterface $user */
        $user = $this->getAdmin();

        $this->client->loginUser($user);

        $this->client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    /**
     * @dataProvider provideRoleAdminUrls
     */
    public function testForbiddenAdminAccess(string $url): void
    {
        $this->databaseTool->loadFixtures([UserFixtures::class]);

        $this->client->request('GET', $url);
        self::assertResponseStatusCodeSame(302);

        /** @var UserInterface $user */
        $user = $this->getUser();

        $this->client->loginUser($user);

        $this->client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider provideRoleUserUrls
     */
    public function testForbiddenUserAccess(string $url): void
    {
        $this->client->request('GET', $url);
        self::assertResponseStatusCodeSame(302);
    }

    /**
     * @throws \Exception
     */
    public function testUserCrudAdminAccessOk(): void
    {
        $this->databaseTool->loadFixtures([UserFixtures::class]);

        /** @var User $admin */
        $admin = $this->getAdmin();
        $id = $admin->getId();

        $urlGenerator = self::getContainer()->get('router.default');

        $this->client->loginUser($admin);

        $this->client->request('GET', $urlGenerator->generate('app_users_update', ['id' => $id]));
        self::assertResponseIsSuccessful();

        // Todo ajouter les autres urls mais pq quand j'en ajoute une autre ça ne fonctionne plus ?
    }

    public function testUserCrudAdminAccessKo(): void
    {
        // Idem ci-dessus
    }

    public static function provideNoAuthUrls(): \Generator
    {
        yield ['/'];
    }

    public static function provideRoleUserUrls(): \Generator
    {
        yield ['/app'];
    }

    public static function provideRoleAdminUrls(): \Generator
    {
        yield ['/comptes'];
        yield ['/comptes/ajouter'];
    }
}
