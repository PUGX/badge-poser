<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SnippetControllerTest extends WebTestCase
{
    public function testAllAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/snippet/all/?repository=pugx/badge-poser');

        $response = $client->getResponse();
        $content = \json_decode((string) $response->getContent(), true);

        self::assertTrue($response->isSuccessful());
        self::assertArrayHasKey('all', $content);
        self::assertArrayHasKey('badges', $content);
    }
}
