<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    /**
     * @dataProvider provider
     */
    public function testHome(string $path): void
    {
        $client = self::createClient();
        $client->request('GET', $path);
        self::assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function provider(): array
    {
        return [
            ['/'],
            ['/show/doctrine/orm'],
        ];
    }
}
