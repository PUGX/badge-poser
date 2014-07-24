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

use Packagist\Api\Client;

class VersionControllerTest extends PackagistWebTestCase
{
    public function testLatestStableAction()
    {
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->createPackagistClient());
        $client->request('GET', '/pugx/badge-poser/version');
        $this->assertTrue($client->getResponse()->isSuccessful(), $client->getResponse()->getContent());
        $this->assertRegExp('/s-maxage=3600/', $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testLatestUnstableAction()
    {
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->createPackagistClient());
        $client->request('GET', '/pugx/badge-poser/v/unstable');

        $this->assertTrue($client->getResponse()->isSuccessful(), $client->getResponse()->getContent());

        $this->assertRegExp('/s-maxage=3600/', $client->getResponse()->headers->get('Cache-Control'));
    }

}
