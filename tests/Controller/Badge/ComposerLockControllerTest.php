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

final class ComposerLockControllerTest extends WebTestCase
{
    public function testComposerLockAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/composerlock');
        self::assertTrue($client->getResponse()->isSuccessful());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }

    public function testComposerLockSvgExplicit(): void
    {
        $client = self::createClient();
        $client->request('GET', '/pugx/badge-poser/composerlock.svg');
        self::assertTrue($client->getResponse()->isSuccessful());

        self::assertMatchesRegularExpression('/max-age=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertMatchesRegularExpression('/s-maxage=3600/', (string) $client->getResponse()->headers->get('Cache-Control'));
    }
}
