<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PackagistControllerTest.
 */
class PackagistControllerTest extends WebTestCase
{
    public function testSearch(): void
    {
        $expectedData = \json_decode('[{"id":"pugx\/badge-poser","description":"Poser, add badges on your readme, such as downloads number or latest version."}]', true);

        $client = static::createClient();
        $client->request('GET', '/search_packagist?name=pugx/badge-poser');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $responseContent = \json_decode($client->getResponse()->getContent(), true);

        foreach ($responseContent as $item) {
            $this->assertNotEmpty($item['id']);
            $this->assertNotEmpty($item['description']);
        }

        $this->assertEquals($expectedData, $responseContent);
    }
}
