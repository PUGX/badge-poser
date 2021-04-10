<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Controller\Badge;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class VersionControllerTest extends WebTestCase
{
    public function testLatestStableAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/version');

        self::assertTrue($client->getResponse()->isSuccessful(), (string) $client->getResponse()->getContent());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testLatestVStableAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/v/stable');

        self::assertTrue($client->getResponse()->isSuccessful(), (string) $client->getResponse()->getContent());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testLatestUnstableAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/v/unstable');

        self::assertTrue($client->getResponse()->isSuccessful(), (string) $client->getResponse()->getContent());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testLatestStableActionSvgExplicit(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/version.svg');
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testLatestUnstableActionSvgExplicit(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/v/unstable.svg');
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testLatestStableActionPngRedirectSvg(): void
    {
        $client = self::createClient();

        $client->request('GET', '/pugx/badge-poser/version.png');
        $crawler = $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('/pugx/badge-poser/version', $crawler->getUri());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }
}
