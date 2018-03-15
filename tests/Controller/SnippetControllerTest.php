<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SnippetControllerTest.
 */
class SnippetControllerTest extends WebTestCase
{
    public function testAllAction(): void
    {
        $expectedData = ['all snippets for pugx/badge-poser'];

        $client = static::createClient();
        $client->request('GET', '/snippet/all/?repository=pugx/badge-poser');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($expectedData, json_decode($client->getResponse()->getContent(), true));
    }
}
