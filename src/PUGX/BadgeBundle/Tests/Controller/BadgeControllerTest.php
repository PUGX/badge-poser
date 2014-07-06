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

class BadgeControllerTest extends PackagistWebTestCase
{

    public function testSearchPackagist()
    {
        $data = '{"results":[{"name":"hpatoio\/deploy-bundle","description":"Brings Symfony 1.4 project:deploy command to Symfony2.","url":"https:\/\/packagist.org\/packages\/hpatoio\/deploy-bundle","downloads":1217,"favers":1},{"name":"hpatoio\/bitly-api","description":"PHP Library based on Guzzle to consume Bit.ly API","url":"https:\/\/packagist.org\/packages\/hpatoio\/bitly-api","downloads":5,"favers":1},{"name":"hpatoio\/bitly-bundle","description":"Integrate hpatoio\/bitly-api in your Symfony2 project","url":"https:\/\/packagist.org\/packages\/hpatoio\/bitly-bundle","downloads":2,"favers":1},{"name":"hpatoio\/commonbackend-bundle","description":"Backend goodies","url":"https:\/\/packagist.org\/packages\/hpatoio\/commonbackend-bundle","downloads":11,"favers":0}],"total":4}';
        $packagistClient = $this->createPackagistClient($data, 200);

        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $packagistClient);
        $client->request('GET', '/search_packagist?name=hpatoio');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
