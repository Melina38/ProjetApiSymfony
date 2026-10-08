<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MoviesControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/movies');

        self::assertResponseIsSuccessful();

        $client->request('GET', '/movies');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/movies?message=coucou');
        self::assertResponseIsSuccessful();

        self::assertJsonStringEqualsJsonString(
            json_encode(['message' => 'coucou']),
            $client->getResponse()->getContent()
        );
    }
}
