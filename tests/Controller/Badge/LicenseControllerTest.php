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

/**
 * Class LicenseControllerTest.
 */
class LicenseControllerTest extends WebTestCase
{
    public function testLicenseAction(): void
    {
        $client = static::createClient();

        $client->request('GET', '/pugx/badge-poser/license');
        $this->assertTrue($client->getResponse()->isSuccessful(), (string) $client->getResponse()->getContent());
    }

    public function testLicenseActionSvgExplicit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/license.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testLicenseActionPngRedirectSvg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/pugx/badge-poser/license.png');
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('/pugx/badge-poser/license', $crawler->getUri());
    }
}
