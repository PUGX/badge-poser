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

final class LicenseControllerTest extends WebTestCase
{
    public function testLicenseAction(): void
    {
        $client = self::createClient();

        $client->request('GET', '/pugx/badge-poser/license');
        self::assertTrue($client->getResponse()->isSuccessful(), (string) $client->getResponse()->getContent());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testLicenseActionSvgExplicit(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/license.svg');
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testLicenseActionPngRedirectSvg(): void
    {
        $client = self::createClient();

        $client->request('GET', '/pugx/badge-poser/license.png');
        $crawler = $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('/pugx/badge-poser/license', (string) $crawler->getUri());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }
}
