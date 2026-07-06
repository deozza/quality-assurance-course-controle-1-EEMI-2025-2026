<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class GameControllerTest extends WebTestCase
{
    public function testCreateGameUnauthorized()
    {
        $client = static::createClient();
        $client->request('POST', '/game');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testGetGames()
    {
        $client = static::createClient();
        $client->request('GET', '/game');
        $this->assertTrue(
            in_array($client->getResponse()->getStatusCode(), [200, 401, 403])
        );
    }

    public function testGetGameUnauthorized()
    {
        $client = static::createClient();
        $client->request('GET', '/game/1');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testDeleteGameUnauthorized()
    {
        $client = static::createClient();
        $client->request('DELETE', '/game/1');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}
