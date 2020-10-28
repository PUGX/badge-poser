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
 * Class CircleCiControllerTest.
 */
class CircleCiControllerTest extends WebTestCase
{
    public function testCircleCi(): void
    {
        $this->markTestSkipped('Temporarly skipped due to a problem with the access key');

        // $client = static::createClient();
        // $client->request('GET', '/pugx/badge-poser/circleci');
        // $this->assertTrue($client->getResponse()->isSuccessful());

        // $this->assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        // $this->assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testCircleCiForBranch(): void
    {
        $this->markTestSkipped('Temporarly skipped due to a problem with the access key');

        // $client = static::createClient();
        // $client->request('GET', '/pugx/badge-poser/circleci/release/v3.0.0');
        // $this->assertTrue($client->getResponse()->isSuccessful());

        // $this->assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        // $this->assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }
}
