<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PngBadgeControllerTest extends WebTestCase
{
    public function testDownloadsPngActionShouldRedirectToSvg()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/d/total.png');
        $this->assertTrue($client->getResponse()->isRedirect('/pugx/badge-poser/d/total'));
    }

    public function testLatestStableAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/version.png');
        $this->assertTrue($client->getResponse()->isRedirect('/pugx/badge-poser/version'));
    }

    public function testLatestUnstableAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/v/unstable.png');
        $this->assertTrue($client->getResponse()->isRedirect('/pugx/badge-poser/v/unstable'));
    }

    public function testLicenseAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/license.png');
        $this->assertTrue($client->getResponse()->isRedirect('/pugx/badge-poser/license'));
    }

    public function testIfPackageDoesntExist()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/microsoft-lover/d/total.png');
        $this->assertTrue($client->getResponse()->isRedirect('/pugx/microsoft-lover/d/total'));
    }
}
