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

final class DownloadsControllerTest extends WebTestCase
{
    public function testDownloadsAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/downloads');
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testDownloadsTotalAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/d/total');
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testDownloadsDailyAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/d/daily');
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testDownloadsMonthlyAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/d/monthly');
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testDownloadsActionSvgExplicit(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/downloads.svg');
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testDownloadsTotalActionSvgExplicit(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/d/total.svg');
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testDownloadsActionPngRedirectSvg(): void
    {
        $client = self::createClient();

        $client->request('GET', '/pugx/badge-poser/downloads.png');
        $crawler = $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('/pugx/badge-poser/downloads', (string) $crawler->getUri());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }
}
