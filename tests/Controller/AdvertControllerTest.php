<?php

namespace App\Test\Controller;

use App\Entity\AdminUser;
use App\Entity\Advert;
use App\Repository\AdminUserRepository;
use App\Repository\AdvertRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdvertControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AdvertRepository $repository;
    private string $path = '/admin/adverts/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Advert::class);
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
        self::assertPageTitleContains('Liste des annonces');
        self::assertSelectorTextContains('h1', 'Liste des annonces');
        self::assertSelectorTextContains('tr', 'Id Titre Contenu Auteur Email Prix Category Photos Etat Publié le Actions');
    }


    public function testShow(): void
    {
        $userRepository = static::getContainer()->get(AdminUserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@gmail.com');
        $this->client->loginUser($testUser);

        $fixture = new Advert();
        $fixture->setId(1111);
        $fixture->setTitle('My Title');
        $fixture->setContent('This is my description.... ahahahahahah fun article');
        $fixture->setAuthor('John Doe');
        $fixture->setEmail('johndoe@gmail.com');
        $fixture->setPrice(200);
        $fixture->setState('draft');
        $fixture->setCategory(null);

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('h1', 'Annonce');
    }

  

}
