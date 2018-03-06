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

class VersionControllerTest extends WebTestCase
{
    public function testLatestStableAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/version');
        $this->assertTrue($client->getResponse()->isSuccessful(), $client->getResponse()->getContent());
        $this->assertRegExp('/s-maxage=3600/', $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testLatestVStableAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/v/stable');

        $this->assertTrue($client->getResponse()->isSuccessful(), $client->getResponse()->getContent());

        $this->assertRegExp('/s-maxage=3600/', $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testLatestUnstableAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/v/unstable');

        $this->assertTrue($client->getResponse()->isSuccessful(), $client->getResponse()->getContent());

        $this->assertRegExp('/s-maxage=3600/', $client->getResponse()->headers->get('Cache-Control'));
    }

}
