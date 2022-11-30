<?php

namespace App\Tests\Api;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Picture;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureApi extends ApiTestCase {
    public function testCollectionGet() : void {
        self::createClient()->request('GET', '/pictures');
        self::assertResponseStatusCodeSame(200);
        self::assertMatchesResourceCollectionJsonSchema(Picture::class);
    }


    // Don't forget to upload an image inside fixtures/files/ with the name image.png before
    public function testCreatePicture() : void {
        $file = new UploadedFile('fixtures/files/image.png', 'image.png');
        $response = self::createClient()->request('POST', '/pictures', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $file,
                ],
            ]
            ]);
        self::assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Picture::class);
    }

    public function testInvalidCreatePicture() : void {
        $response = self::createClient()->request('POST', '/pictures', ['json' =>[
            'path' => 'string',
            ]]);
        self::assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testInvalidGetById() : void {
        $response = self::createClient()->request('GET', '/pictures/toto');
        self::assertResponseStatusCodeSame(404);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    
    public function testNotDefineDeleteItem() : void {
        self::createClient()->request('DELETE', '/pictures/toto');
        self::assertResponseStatusCodeSame(405);
    }

    public function testNotPatchItem() : void {
        self::createClient()->request('PATCH', '/pictures/toto');
        self::assertResponseStatusCodeSame(405);
    }

    public function testNotPutItem() : void {
        self::createClient()->request('PUT', '/pictures/toto');
        self::assertResponseStatusCodeSame(405);
    }


}

