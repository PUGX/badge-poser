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

/**
 * Class CircleCiControllerTest.
 */
class CircleCiControllerTest extends WebTestCase
{
    public function testCircleCi(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/circleci');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testCircleCiForBranch(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/circleci/release/v3.0.0');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
