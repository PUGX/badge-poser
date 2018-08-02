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
 * Class GitAttributesBadgeControllerTest.
 */
class GitAttributesBadgeControllerTest extends WebTestCase
{
    public function testGitAttributesAction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/gitattributes');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testGitAttributesSvgExplicit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/gitattributes.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
