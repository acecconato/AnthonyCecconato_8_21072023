<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserControllerTest extends WebTestCase
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

        $this->client->catchExceptions(false);
        self::expectException(AccessDeniedException::class);

        $this->client->request('GET', '/comptes');
    }

    public function testCreateUser(): void
    {
        /** @var User $admin */
        $admin = $this->manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', 'comptes/ajouter');

        $form = $crawler->selectButton('submit')->form([
            'user[email]' => 'john@doe.fr',
            'user[username]' => 'JohnDoe',
            'user[plainPassword]' => 'AStrongPassword112233!',
            'user[role]' => 'ROLE_USER',
        ]);

        $this->client->submit($form);

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        /** @var User $createdUser */
        $createdUser = $this->manager->getRepository(User::class)->findOneBy(['username' => 'JohnDoe']);
        self::assertNotNull($createdUser->getId());
    }

    public function testCreateUserAccessDenied(): void
    {
        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);
        $this->client->loginUser($user);

        self::expectException(AccessDeniedException::class);
        $this->client->catchExceptions(false);
        $this->client->request('GET', 'comptes/ajouter');
    }

    public function testUpdateUser(): void
    {
        /** @var User $admin */
        $admin = $this->manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($admin);

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);
        $crawler = $this->client->request('GET', 'comptes/'.$user->getId().'/modifier');

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $form->setValues([
            'user[email]' => 'john@doe.fr',
            'user[username]' => 'JohnDoe',
            'user[plainPassword]' => 'AStrongPassword112233!',
            'user[role]' => 'ROLE_ADMIN',
        ]);

        $this->client->submit($form);

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        /** @var User $updatedUser */
        $updatedUser = $this->manager->getRepository(User::class)->find($user->getId());
        self::assertEquals('john@doe.fr', $updatedUser->getEmail());
        self::assertEquals('JohnDoe', $updatedUser->getUsername());
        self::assertEquals('ROLE_ADMIN', $updatedUser->getRole());
    }

    public function testUpdateUserWithoutPassword(): void
    {
        /** @var User $admin */
        $admin = $this->manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($admin);

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);
        $crawler = $this->client->request('GET', 'comptes/'.$user->getId().'/modifier');

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $form->setValues([
            'user[email]' => 'john@doe.fr',
            'user[username]' => 'JohnDoe',
            'user[role]' => 'ROLE_ADMIN',
        ]);

        $this->client->submit($form);

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        /** @var User $updatedUser */
        $updatedUser = $this->manager->getRepository(User::class)->find($user->getId());
        self::assertEquals('john@doe.fr', $updatedUser->getEmail());
        self::assertEquals('JohnDoe', $updatedUser->getUsername());
        self::assertEquals('ROLE_ADMIN', $updatedUser->getRole());
    }

    public function testDeleteUserAccessDenied(): void
    {
        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);
        $this->client->loginUser($user);

        self::expectException(AccessDeniedException::class);
        $this->client->catchExceptions(false);

        $this->client->request('GET', 'comptes/'.$user->getId().'/supprimer');
    }

    public function testDeleteUser(): void
    {
        /** @var User $admin */
        $admin = $this->manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($admin);

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);

        $this->client->request('GET', 'comptes'); // for referer redirection
        $this->client->request('GET', 'comptes/'.$user->getId().'/supprimer');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertNull($this->manager->find(User::class, $user->getId()));
    }

    public function testUpdateUserRoleAccessDenied(): void
    {
        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);
        $this->client->loginUser($user);

        self::expectException(AccessDeniedException::class);
        $this->client->catchExceptions(false);

        $this->client->request('GET', 'comptes/'.$user->getId().'/modifier-role');
    }

    public function testUpdateUserRoleToAdmin(): void
    {
        /** @var User $admin */
        $admin = $this->manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($admin);

        $this->client->request('GET', 'comptes'); // for referer redirection

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'demo']);
        $this->client->request('GET', 'comptes/'.$user->getId().'/modifier-role');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        /** @var User $updatedUser */
        $updatedUser = $this->manager->find(User::class, $user->getId());

        self::assertEquals('ROLE_ADMIN', $updatedUser->getRole());
        self::assertTrue(in_array('ROLE_ADMIN', $updatedUser->getRoles(), true));
        self::assertCount(1, $updatedUser->getRoles());
    }

    public function testUpdateUserRoleToUser(): void
    {
        /** @var User $admin */
        $admin = $this->manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($admin);

        $this->client->request('GET', 'comptes'); // for referer redirection

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => 'user1']);
        $this->client->request('GET', 'comptes/'.$user->getId().'/modifier-role');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        /** @var User $updatedUser */
        $updatedUser = $this->manager->find(User::class, $user->getId());

        self::assertEquals(null, $updatedUser->getRole());
        self::assertTrue(in_array('ROLE_USER', $updatedUser->getRoles(), true));
        self::assertCount(1, $updatedUser->getRoles());
    }
}
