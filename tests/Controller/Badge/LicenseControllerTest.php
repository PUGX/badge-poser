<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Basge\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LicenseControllerTest extends WebTestCase
{
    public function testLicenseAction()
    {
        $client = static::createClient();

        $client->request('GET', '/pugx/badge-poser/license');
        $this->assertTrue($client->getResponse()->isSuccessful(), $client->getResponse()->getContent());
    }

    public function testLicenseActionSvgExplicit()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/license.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testLicenseActionPngRedirectSvg()
    {
        $client = static::createClient();

        $client->request('GET', '/pugx/badge-poser/license.png');
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('/pugx/badge-poser/license', $crawler->getUri());
    }
}
