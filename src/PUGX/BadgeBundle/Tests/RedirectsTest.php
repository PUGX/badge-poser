<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RedirectsTest extends WebTestCase
{
    /**
     * @dataProvider getUrlToVerify
     */
    public function testOldPageShouldRedirectToNewOnes($oldUrl, $newUrl)
    {
        $client = static::createClient();
        $client->request('GET', $oldUrl);
        $this->assertTrue($client->getResponse()->isRedirect($newUrl));
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
    }

    public function getUrlToVerify()
    {
        return array(
            array('/pugx/badge-poser/downloads.png', 'http://localhost/pugx/badge-poser/downloads'),
            array('/pugx/badge-poser/d/total.png', 'http://localhost/pugx/badge-poser/d/total'),
            array('/pugx/badge-poser/version.png', 'http://localhost/pugx/badge-poser/version'),
            array('/pugx/badge-poser/v/unstable.png', 'http://localhost/pugx/badge-poser/v/unstable'),
            array('/pugx/badge-poser/license.png', 'http://localhost/pugx/badge-poser/license'),
            array('/pugx/microsoft-lover/d/total.png', 'http://localhost/pugx/microsoft-lover/d/total'),
        );
    }
}
