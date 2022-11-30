<?php

namespace App\Test\Controller;

use App\Entity\Category;
use App\Repository\AdminUserRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CategoryRepository $repository;
    private string $path = '/admin/category/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Category::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testRedirectionToLogin() : void {
        $crawler = $this->client->request('GET', $this->path);
        self::assertResponseStatusCodeSame(302);
    }

    public function testIndex(): void
    {
        $userRepository = static::getContainer()->get(AdminUserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Liste des catégories');
        self::assertSelectorTextContains('h1', 'Liste des catégories');
        self::assertSelectorTextContains('tr', 'Id Nom Annonces Actions');
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
            'category[name]' => 'Testing',
        ]);

        self::assertResponseRedirects('/admin/category/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $userRepository = static::getContainer()->get(AdminUserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($testUser);

        $fixture = new Category();
        $fixture->setName('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Catégorie');
        self::assertSelectorTextContains('h1', 'Catégorie');
    }

    public function testEdit(): void
    {
        $userRepository = static::getContainer()->get(AdminUserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($testUser);

        $fixture = new Category();
        $fixture->setName('My Cat');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'category[name]' => 'SomethingNew',
        ]);

        self::assertResponseRedirects('/admin/category/');

        $fixture = $this->repository->findOneByName('SomethingNew');

        self::assertSame('SomethingNew', $fixture->getName());
    }

    public function testRemove(): void
    {
        $userRepository = static::getContainer()->get(AdminUserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($testUser);

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Category();
        $fixture->setName('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Supprimer');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/category/');
    }
}
