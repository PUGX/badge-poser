<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\StatsBundle\Service;

use PUGX\StatsBundle\ChartElement;

/**
 * Class RedisStats
 *
 * @author Simone Fumagalli <simone@iliveinperego.com>
 */
class RedisReader implements ReaderInterface
{
    private $redis;
    /** @var KeysCreator  */
    private $keysCreator;

    public function __construct($redis, $keysCreator)
    {
        $this->redis = $redis;
        $this->keysCreator = $keysCreator;
    }

    /**
     * Read total accesses.
     *
     * @return integer
     */
    public function totalAccess()
    {
        return $this->redis->get($this->keysCreator->getKeyTotal());
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string    $dimension
     *
     * @return ChartElement[]
     */
    public function totalDataOfAccessesByInterval(\DateTime $startDate, \DateTime $endDate, $dimension = ReaderInterface::MONTH)
    {
        $period = $this->createPeriod($startDate, $endDate, $dimension);
        $data = array();
        foreach($period as $interval){
            $value = $this->redis->get($this->getKeyString($interval, $dimension));

            $data[] = new ChartElement($interval, $value);
        }

        return $data;
    }

    private function getKeyString(\Datetime $date, $dimension)
    {
        if (ReaderInterface::DAY == $dimension) {
            return $this->keysCreator->createDailyKey($date);
        }

        if (ReaderInterface::MONTH == $dimension) {
            return $this->keysCreator->createMonthlyKey($date);
        }

        return $this->keysCreator->createYearlyKey($date);
    }

    private function createPeriod(\DateTime $startDate, \DateTime $endDate, $dimension = ReaderInterface::MONTH)
    {
        $periodInt = new \DateInterval("P1".$dimension);
        return new \DatePeriod( $startDate, $periodInt, $endDate );
    }
}
