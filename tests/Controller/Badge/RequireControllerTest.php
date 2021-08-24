<?php

namespace App\Tests\Controller\Badge;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RequireControllerTest extends WebTestCase
{
    public function testRequireAction(): void
    {
        $client = self::createClient();

        $client->request('GET', '/pugx/badge-poser/require/php');
        self::assertTrue($client->getResponse()->isSuccessful(), (string) $client->getResponse()->getContent());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testRequireActionSvgExplicit(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/require/php.svg');
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testRequirePackageAction(): void
    {
        $client = self::createClient();

        $client->request('GET', '/pugx/badge-poser/require/badges/poser');
        self::assertTrue($client->getResponse()->isSuccessful(), (string) $client->getResponse()->getContent());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }
}
