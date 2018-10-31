<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\StatsBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PUGX\StatsBundle\Service\RedisPersister;

class StatsListenerTest extends WebTestCase
{
	private $redis_persister;

	public function setUp()
    {
    	$redis = $this->getRedis();

        $this->redis_persister = new RedisPersister($redis);
    }

    public function testIncrementTotalAccessReturnsPersister()
    {
    	$test_object = $this->redis_persister->incrementTotalAccess();

    	self::assertTrue($test_object instanceof RedisPersister);
    }

    private function getRedis()
    {
    	return new class {

			public function incr() { 
    		}

			public function createDailyKey() { 
    		}

			public function createMonthlyKey($arg) { 

    		}

			public function createYearlyKey($arg) { 

    		}

		};
    }
}