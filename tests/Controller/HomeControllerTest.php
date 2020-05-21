<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    /**
     * @dataProvider provider
     */
    public function testHome(string $path): void
    {
        $client = static::createClient();
        $client->request('GET', $path);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @return array<array<string>>
     */
    public function provider(): array
    {
        return [
            ['/'],
            ['/show/doctrine/orm'],
        ];
    }
}
