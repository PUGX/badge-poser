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
 * Class SuggestersControllerTest.
 */
class SuggestersControllerTest extends WebTestCase
{
    public function testSuggestersAction(): void
    {
        $client = static::createClient();

        $client->request('GET', '/pugx/badge-poser/suggesters');
        $this->assertTrue($client->getResponse()->isSuccessful(), (string) $client->getResponse()->getContent());

        $this->assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        $this->assertMatchesRegularExpression('/s-maxage=86400/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testSuggestersActionSvgExplicit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/suggesters.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        $this->assertMatchesRegularExpression('/s-maxage=86400/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }
}
