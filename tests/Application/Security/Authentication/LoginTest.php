<?php

namespace App\Tests\Application\Security\Authentication;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    protected KernelBrowser $client;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
    }

    public function testLoginSuccess(): void
    {
        $crawler = $this->client->request('GET', '/');

        $buttonCrawlerNode = $crawler->selectButton('_submit');
        $form = $buttonCrawlerNode->form();

        $data = ['_username' => 'demo', '_password' => 'demo'];

        $this->client->submit($form, $data);

        $this->client->followRedirect();

        $user = $this->client->getContainer()->get('security.token_storage')->getToken()?->getUser();
        self::assertInstanceOf(User::class, $user);
    }

    public function testLoginIncorrectCredentials(): void
    {
        $crawler = $this->client->request('GET', '/');

        $buttonCrawlerNode = $crawler->selectButton('_submit');
        $form = $buttonCrawlerNode->form();

        $data = ['_username' => 'no_account', '_password' => 'no_account'];

        $this->client->submit($form, $data);

        $this->client->followRedirect();

        $userToken = $this->client->getContainer()->get('security.token_storage')->getToken();
        self::assertNull($userToken);
    }
}
