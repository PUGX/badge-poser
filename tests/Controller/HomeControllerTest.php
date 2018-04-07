<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    /**
     * @dataProvider provider
     * @param $path
     */
    public function testHome($path): void
    {
        $client = static::createClient();
        $client->request('GET', $path);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function provider(): array
    {
        return [
            ['/'],
            ['/show/doctrine/orm'],
        ];
    }
}
