<?php

namespace App\Test\Controller;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdvertControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AdvertRepository $repository;
    private string $path = '/advert/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Advert::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Liste des annonces');
        self::assertSelectorTextContains('h1', 'Liste des annonces');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }


    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Advert();
        $fixture->setTitle('My Title');
        $fixture->setContent('This is my description.... ahahahahahah fun article');
        $fixture->setAuthor('John Doe');
        $fixture->setEmail('johndoe@gmail.com');
        $fixture->setPrice(200);
        $fixture->setState(draft);
        $fixture->setCategory('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Advert');

        // Use assertions to check that the properties are properly displayed.
    }

  

}
