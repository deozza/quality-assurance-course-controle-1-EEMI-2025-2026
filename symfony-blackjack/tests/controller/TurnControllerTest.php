<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TurnControllerTest extends WebTestCase
{
    public function testCreateTurnUnauthorized()
    {
        $client = static::createClient();
        $client->request('POST', '/game/1/turn');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testGetTurnUnauthorized()
    {
        $client = static::createClient();
        $client->request('GET', '/turn/1');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testHitUnauthorized()
    {
        $client = static::createClient();
        $client->request('PATCH', '/turn/1/hit');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testStandUnauthorized()
    {
        $client = static::createClient();
        $client->request('PATCH', '/turn/1/stand');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testWageTurnUnauthorized()
    {
        $client = static::createClient();
        $client->request(
            'PATCH',
            '/turn/1/wage',
            [],
            [],
            [],
            json_encode(['amount' => 100])
        );
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}