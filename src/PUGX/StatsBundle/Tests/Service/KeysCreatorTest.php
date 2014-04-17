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

use PUGX\StatsBundle\Service\KeysCreator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class KeysCreatorTest extends WebTestCase
{
    public function testCreateYearlyKey()
    {
        $key = new KeysCreator();

        $this->assertEquals('STAT.TOTAL_2000', $key->createYearlyKey(new \DateTime('2000-01-01')));
    }

    public function testCreateMonthlyKey()
    {
        $key = new KeysCreator();

        $this->assertEquals('STAT.TOTAL_2000_01', $key->createMonthlyKey(new \DateTime('2000-01-01')));
    }

    public function testCreateDailyKey()
    {
        $key = new KeysCreator();

        $this->assertEquals('STAT.TOTAL_2000_01_01', $key->createDailyKey(new \DateTime('2000-01-01')));
    }

    public function testGetKeyHashWithNoRepository()
    {
        $key = new KeysCreator();

        $this->assertEquals('STAT.REPO', $key->getKeyHash());
    }

    public function testGetKeyHashWithRepository()
    {
        $key = new KeysCreator();

        $this->assertEquals('STAT.REPO.repo/vendor', $key->getKeyHash('repo/vendor'));
    }

    public function testGetRefererKey()
    {
        $key = new KeysCreator();

        $this->assertEquals('STAT.LIST.REFE', $key->getRefererKey());
    }
}
