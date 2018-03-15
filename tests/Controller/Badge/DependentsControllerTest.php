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
 * Class DependentsControllerTest.
 */
class DependentsControllerTest extends WebTestCase
{
    public function testDependentsAction(): void
    {
        $client = static::createClient();

        $client->request('GET', '/pugx/badge-poser/dependents');
        $this->assertTrue($client->getResponse()->isSuccessful(), $client->getResponse()->getContent());
    }

    public function testDependentsActionSvgExplicit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/dependents.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
