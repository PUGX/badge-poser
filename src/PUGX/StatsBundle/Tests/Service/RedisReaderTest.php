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

use PUGX\StatsBundle\ChartElement;
use PUGX\StatsBundle\Service\RedisReader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RedisReaderTest extends WebTestCase
{

    public function testTotalDataOfAccessesByIntervalYear()
    {
        $redis = $this->getMock('\stdClass', array('get'));
        $keysCreator = $this->getMock('\PUGX\StatsBundle\Service\KeysCreator');

        $reader = new RedisReader($redis, $keysCreator);

        $from = new \DateTime('2000-01-01');
        $to = new \DateTime('2000-02-01');

        $element = new ChartElement(new \DateTime('2000-01-01 00:00:00'), null);

        $this->assertEquals(array($element), $reader->totalDataOfAccessesByInterval($from, $to, RedisReader::YEAR));
    }

    public function testTotalDataOfAccessesByIntervalMonth()
    {
        $redis = $this->getMock('\stdClass', array('get'));
        $keysCreator = $this->getMock('\PUGX\StatsBundle\Service\KeysCreator');

        $reader = new RedisReader($redis, $keysCreator);

        $from = new \DateTime('2000-01-01');
        $to = new \DateTime('2000-03-01');

        $element = array();
        $element[] = new ChartElement(new \DateTime('2000-01-01 00:00:00'), null);
        $element[] = new ChartElement(new \DateTime('2000-02-01 00:00:00'), null);

        $this->assertEquals($element, $reader->totalDataOfAccessesByInterval($from, $to, RedisReader::MONTH));
    }


    public function testTotalDataOfAccessesByIntervalDay()
    {
        $redis = $this->getMock('\stdClass', array('get'));
        $keysCreator = $this->getMock('\PUGX\StatsBundle\Service\KeysCreator');

        $reader = new RedisReader($redis, $keysCreator);

        $from = new \DateTime('2000-01-01');
        $to = new \DateTime('2000-01-03');

        $element = array();
        $element[] = new ChartElement(new \DateTime('2000-01-01 00:00:00'), null);
        $element[] = new ChartElement(new \DateTime('2000-01-02 00:00:00'), null);

        $this->assertEquals($element, $reader->totalDataOfAccessesByInterval($from, $to, RedisReader::DAY));
    }
}
