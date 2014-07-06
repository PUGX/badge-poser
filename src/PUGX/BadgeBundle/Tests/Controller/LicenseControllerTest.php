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

class LicenseControllerTest extends PackagistWebTestCase
{
    public function testLicenseAction()
    {
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->createPackagistClient());

        $client->request('GET', '/pugx/badge-poser/license');
        $this->assertTrue($client->getResponse()->isSuccessful(), $client->getResponse()->getContent());
    }
}
