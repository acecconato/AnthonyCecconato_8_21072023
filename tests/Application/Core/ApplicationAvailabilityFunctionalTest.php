<?php

declare(strict_types=1);

namespace App\Tests\Application\Core;

use App\Doctrine\DataFixtures\AppFixtures;
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

    /**
     * @dataProvider provideNoAuthUrls
     */
    public function testPageIsSuccessful(string $url): void
    {
        $this->client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }

    /**
     * @dataProvider provideAuthRequiredUrls
     */
    public function testAuthRequiredPageIsSuccessful(string $url): void
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);

        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        /** @var UserInterface $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => 'demo']);

        $this->client->loginUser($user);

        $this->client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    public static function provideNoAuthUrls(): \Generator
    {
        yield ['/'];
    }

    public static function provideAuthRequiredUrls(): \Generator
    {
        yield ['/app'];
    }
}
