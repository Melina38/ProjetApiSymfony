<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MoviesControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/movies');

        //je m'attend a ce que le code de réponse soit 200
        self::assertResponseIsSuccessful();

        //faire un appel de la route sans query parameter
        $client->request('GET', '/movies');
        self::assertResponseIsSuccessful();

        //faire un appel de la route avec query parameter
        $client->request('GET', '/movies?message=coucou');
        self::assertResponseIsSuccessful();

        //s'assure que la réponse est bien celle attendue
        self::assertJsonStringEqualsJsonString(
            json_encode(['message' => 'coucou']),
            $client->getResponse()->getContent()
        );
    }
}
