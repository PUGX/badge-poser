<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PackagistControllerTest extends WebTestCase
{
    public function testSearch(): void
    {
        $expectedData = \json_decode('[{"id":"pugx\/badge-poser","description":"Poser, add badges on your readme, such as downloads number or latest version."}]', true);

        $client = self::createClient();
        $client->request('GET', '/search_packagist?name=pugx/badge-poser');

        self::assertResponseIsSuccessful();

        $responseContent = \json_decode((string) $client->getResponse()->getContent(), true);

        foreach ($responseContent as $item) {
            self::assertNotEmpty($item['id']);
            self::assertNotEmpty($item['description']);
        }

        self::assertEquals($expectedData, $responseContent);
    }

    public function testLimitedSearch(): void
    {
        $client = self::createClient();
        $client->request('GET', '/search_packagist?name=symfony');

        self::assertResponseIsSuccessful();

        $responseContent = \json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertCount(15, $responseContent);
    }
}
