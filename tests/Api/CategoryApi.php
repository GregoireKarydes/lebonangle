<?php

namespace App\Tests\Api;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryApi extends ApiTestCase {


    public function testCollectionGet() : void {
        self::createClient()->request('GET', '/categories');
        self::assertResponseStatusCodeSame(200);
        self::assertMatchesResourceCollectionJsonSchema(Category::class);
    }


    public function testInvalidGetById() : void {
        self::createClient()->request('GET', '/categories/toto');
        self::assertResponseStatusCodeSame(404);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }


    public function testNotDefineDeleteItem() : void {
        self::createClient()->request('DELETE', '/categories/toto');
        self::assertResponseStatusCodeSame(405);
    }

    public function testNotPatchItem() : void {
        self::createClient()->request('PATCH', '/categories/toto');
        self::assertResponseStatusCodeSame(405);
    }

    public function testNotPutItem() : void {
        self::createClient()->request('PUT', '/categories/toto');
        self::assertResponseStatusCodeSame(405);
    }

    public function testNotDefineCreate() : void {
        self::createClient()->request('POST', '/categories');
        self::assertResponseStatusCodeSame(405);
    }
}

