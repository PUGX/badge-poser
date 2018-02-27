<?php

namespace PUGX\BadgeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    /**
     * @dataProvider provider
     */
    public function testHomeAction($path)
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', $path);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function provider()
    {
        return [
            ['/'],
            ['/show/doctrine/orm']
        ];
    }

}
