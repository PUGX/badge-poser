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
use PUGX\StatsBundle\Service\NullPersister;

class PageControllerTest extends WebTestCase
{
    public function testHomeAction()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('#container h1:contains("Badge Poser")')->count());
        $this->assertEquals(1, $crawler->filter('#container p:contains("Pimp your README!")')->count());
        $this->assertEquals(1, $crawler->filter('#container h4:contains("Total downloads")')->count());
        $this->assertEquals(1, $crawler->filter('#container h4:contains("Daily downloads")')->count());
        $this->assertEquals(1, $crawler->filter('#container h4:contains("Monthly downloads")')->count());
        $this->assertEquals(1, $crawler->filter('#container h4:contains("Latest Stable Version")')->count());
        $this->assertEquals(1, $crawler->filter('#container h4:contains("Latest Unstable Version")')->count());

        $profile = $client->getProfile();
        $eventCollector = $profile->getCollector('events');
        $eventName = 'kernel.controller.PUGX\StatsBundle\Listener\StatsListener::onKernelController';
        $this->assertArrayHasKey($eventName, $eventCollector->getCalledListeners(), "stats listener has been called") ;

        $this->assertFalse(NullPersister::$incrementTotalAccessCalled, "stats increment method 'incrementTotalAccess' should not be called");
        $this->assertFalse(NullPersister::$incrementRepositoryAccessCalled, "stats increment method 'incrementRepositoryAccess' should not be called");
        $this->assertFalse(NullPersister::$addRepositoryToLatestAccessedCalled, "stats increment method 'addRepositoryToLatestAccessed' should not be called");
        $this->assertFalse(NullPersister::$incrementRepositoryAccessTypeCalled, "stats increment method 'incrementRepositoryAccessType' should not be called");
    }

    public function tearDown()
    {
        NullPersister::$incrementTotalAccessCalled = false;
        NullPersister::$incrementRepositoryAccessCalled = false;
        NullPersister::$incrementRepositoryAccessTypeCalled = false;
    }
}
