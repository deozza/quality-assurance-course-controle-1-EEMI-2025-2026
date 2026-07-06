<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlackjackFlowTest extends WebTestCase
{
    public function testInvalidJsonOnWage()
    {
        $client = static::createClient();
        $client->request('PATCH', '/turn/1/wage', [], [], [], 'INVALID_JSON');
        $this->assertTrue(
            in_array($client->getResponse()->getStatusCode(), [400, 401])
        );
    }

    public function testGameLifecycleUnauthorized()
    {
        $client = static::createClient();
      
        $client->request('POST', '/game');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client->request('POST', '/game/1/turn');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client->request('PATCH', '/turn/1/hit');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client->request('PATCH', '/turn/1/stand');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}