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
 * Class SuggestersControllerTest.
 */
class SuggestersControllerTest extends WebTestCase
{
    public function testSuggestersAction(): void
    {
        $client = static::createClient();

        $client->request('GET', '/pugx/badge-poser/suggesters');
        $this->assertTrue($client->getResponse()->isSuccessful(), $client->getResponse()->getContent());
    }

    public function testSuggestersActionSvgExplicit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/suggesters.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
