<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\StatsBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PUGX\StatsBundle\Service\NullPersister;
use Symfony\Bundle\FrameworkBundle\Client;

class StatsFunctionalTest extends WebTestCase
{
    /**
     * @param Client $client
     */
    public function checkStatsAreNotIncremented(Client $client)
    {
        $this->checkEventIsCaught($client);

        $this->assertFalse(NullPersister::$incrementTotalAccessCalled, "stats increment method 'incrementTotalAccess' should not be called");
        $this->assertFalse(NullPersister::$incrementRepositoryAccessCalled, "stats increment method 'incrementRepositoryAccess' should not be called");
        $this->assertFalse(NullPersister::$addRepositoryToLatestAccessedCalled, "stats increment method 'addRepositoryToLatestAccessed' should not be called");
        $this->assertFalse(NullPersister::$incrementRepositoryAccessTypeCalled, "stats increment method 'incrementRepositoryAccessType' should not be called");
    }

    /**
     * @param Client $client
     * @param string $repo
     * @param string $action
     */
    public function checkStatsCalls(Client $client, $repo, $action)
    {

        $this->checkEventIsCaught($client);

        $this->assertTrue(NullPersister::$incrementTotalAccessCalled, "stats total access increment not called");
        $this->assertEquals($repo, NullPersister::$incrementRepositoryAccessCalled, "stats repo access increment not called or called with wrong param (repo: " . $repo . ")");
        $this->assertEquals($repo, NullPersister::$addRepositoryToLatestAccessedCalled, "stats repo last access increment not called or called with wrong param (repo: " . $repo . ")");
        $this->assertEquals(array($repo, $action), NullPersister::$incrementRepositoryAccessTypeCalled, "stats repo access type increment not called or called with wrong params (repo: " . $repo . "; action: " . $action . ")");
    }

    /**
     * @param  Client     $client
     * @throws \Exception
     */
    public function checkEventIsCaught(Client $client)
    {
        $profile = $client->getProfile();

        if (!$profile) {
            throw new \Exception("Hey Poser! The profiler should be enabled if you want to check events");
        }

        $eventCollector = $profile->getCollector('events');
        $eventName = 'kernel.controller.PUGX\StatsBundle\Listener\StatsListener::onKernelController';
        $this->assertArrayHasKey($eventName, $eventCollector->getCalledListeners(), "stats listener has been called");
    }

    public function tearDown()
    {
        NullPersister::$incrementTotalAccessCalled = false;
        NullPersister::$incrementRepositoryAccessCalled = false;
        NullPersister::$addRepositoryToLatestAccessedCalled = false;
        NullPersister::$incrementRepositoryAccessTypeCalled = false;

        parent::tearDown();
    }
}
