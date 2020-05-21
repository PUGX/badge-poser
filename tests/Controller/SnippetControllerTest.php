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
        $client = static::createClient();
        $client->request('GET', '/snippet/all/?repository=pugx/badge-poser');

        $response = $client->getResponse();
        $content = \json_decode((string) $response->getContent(), true);

        $this->assertTrue($response->isSuccessful());
        $this->assertArrayHasKey('all', $content);
        $this->assertArrayHasKey('badges', $content);
    }
}
