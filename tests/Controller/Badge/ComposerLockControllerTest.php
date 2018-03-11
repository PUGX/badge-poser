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
 * Class ComposerLockControllerTest
 * @package App\Tests\Basge\Controller
 */
class ComposerLockControllerTest extends WebTestCase
{
    public function testComposerLockAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/composerlock');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testComposerLockSvgExplicit()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/composerlock.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}
