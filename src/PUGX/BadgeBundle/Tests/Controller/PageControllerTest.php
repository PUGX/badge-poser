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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    /**
     * @dataProvider provider
     */
    public function testHomeAction($path)
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', $path);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('#container h1:contains("Badge Poser")')->count());
        $this->assertEquals(1, $crawler->filter('#container p:contains("Pimp your README")')->count());
        $this->assertEquals(1, $crawler->filter('#container h4:contains("Total downloads")')->count());
        $this->assertEquals(1, $crawler->filter('#container h4:contains("Daily downloads")')->count());
        $this->assertEquals(1, $crawler->filter('#container h4:contains("Monthly downloads")')->count());
        $this->assertEquals(1, $crawler->filter('#container h4:contains("Latest Stable Version")')->count());
        $this->assertEquals(1, $crawler->filter('#container h4:contains("Latest Unstable Version")')->count());
    }

    public function provider()
    {
        return array(
            array('/'),
            array('/show/doctrine/orm')
        );
    }

}
