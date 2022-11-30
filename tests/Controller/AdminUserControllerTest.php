<?php

namespace App\Test\Controller;

use App\Entity\AdminUser;
use App\Repository\AdminUserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminUserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AdminUserRepository $repository;
    private string $path = '/admin/admin-user/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(AdminUser::class);

    }

    public function testRedirectionToLogin() : void {
        $this->client->request('GET', $this->path);
        self::assertResponseStatusCodeSame(302);
    }

    public function testIndex(): void
    {
        $userRepository = static::getContainer()->get(AdminUserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($testUser);

        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Liste des admins');
        self::assertSelectorTextContains('h1', 'Liste des admins');
        self::assertSelectorTextContains('tr', 'Id Username Email actions');
    }

    public function testNew(): void
    {
        $userRepository = static::getContainer()->get(AdminUserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($testUser);
        
        $originalNumObjectsInRepository = count($this->repository->findAll());
        
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Sauvegarder', [
            'admin_user[username]' => 'Testing',
            'admin_user[email]' => 'random23@gmail.com',
            'admin_user[plainpassword]' => 'CDjbnecmùln54',
        ]);

        self::assertResponseRedirects('/admin/admin-user/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $userRepository = static::getContainer()->get(AdminUserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($testUser);

        $fixture = new AdminUser();
        $fixture->setUsername('username');
        $fixture->setEmail('username@gmail.com');
        $fixture->setPassword('ijhvbhqsjkbvsbk');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Détails');
        self::assertSelectorTextContains('h1', 'Détails');

    }

    public function testEdit(): void
    {
        $userRepository = static::getContainer()->get(AdminUserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($testUser);

        $fixture = new AdminUser();
        $fixture->setUsername('username');
        $fixture->setEmail('old24556@gmail.com');
        $fixture->setPassword('sbffbfbfb');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        var_dump($this->client->getResponse()->getContent());
        $this->client->submitForm('Mettre à jour', [
            'admin_user[username]' => 'newusername',
            'admin_user[email]' => 'latest1213@gmail.com',
            'admin_user[plainpassword]' => 'SomethingNew5654654',
        ]);

        self::assertResponseRedirects('/admin/admin-user/');

        $fixture = $this->repository->findOneByEmail('latest1213@gmail.com');

        self::assertSame('newusername', $fixture->getUsername());
        self::assertSame('latest1213@gmail.com', $fixture->getEmail());
    }

    public function testRemove(): void
    {
        $userRepository = static::getContainer()->get(AdminUserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($testUser);

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new AdminUser();
        $fixture->setUsername('toto');
        $fixture->setEmail('toto123@gmail.com');
        $fixture->setPassword('e54ef46e54ef');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Supprimer');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/admin-user/');
    }
}
