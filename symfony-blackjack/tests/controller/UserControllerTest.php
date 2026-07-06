<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class UserControllerTest extends WebTestCase
{
    public function testCreateUser()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/user',
            [],
            [],
            [],
            json_encode([
                'email' => 'test@test.com',
                'password' => '123456'
            ])
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testGetUsersRequiresAdmin()
    {
        $client = static::createClient();
        $client->request('GET', '/user');
        $this->assertTrue(
            in_array($client->getResponse()->getStatusCode(), [401, 403])
        );
    }

    public function testGetProfileUnauthorized()
    {
        $client = static::createClient();
        $client->request('GET', '/user/profile');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}
