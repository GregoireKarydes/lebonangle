<?php

namespace App\Tests\Api;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Advert;

class AdvertApi extends ApiTestCase {
    public function testCollectionGet() : void {
        self::createClient()->request('GET', '/adverts');
        self::assertResponseStatusCodeSame(200);
        self::assertMatchesResourceCollectionJsonSchema(Advert::class);
    }


    public function testCreateAdvert() : void {
        $response = self::createClient()->request('POST', '/adverts', ['json' =>[
            'title' => 'string',
            'content' => 'string',
            'author' => 'string',
            'email' => 'user@example.com',
            'category' => '/categories/2',
            'price' => 100,
            'pictures' => []
            ]]);
        self::assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesRegularExpression('~^/adverts/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Advert::class);
    }
}

