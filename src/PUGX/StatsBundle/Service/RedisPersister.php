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

/**
 * Class RedisPersister
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class RedisPersister implements PersisterInterface
{
    private $redis;


    public function __construct($redis, KeysCreator $keysCreator)
    {
        $this->redis = $redis;
        $this->keysCreator = $keysCreator;
    }


    /**
     * Increment by one the total accesses.
     *
     * @return PersisterInterface
     */
    public function incrementTotalAccess()
    {

        $this->redis->incr($this->keysCreator->getKeyTotal());

        $key = $this->keysCreator->createDailyKey();
        $this->redis->incr($key);

        $key = $this->keysCreator->createMonthlyKey();
        $this->redis->incr($key);

        $key = $this->keysCreator->createYearlyKey();
        $this->redis->incr($key);

        return $this;
    }

    /**
     * Increment by one the repository accesses.
     *
     * @param string $repository
     *
     * @return PersisterInterface
     */
    public function incrementRepositoryAccess($repository)
    {
        $hash = $this->keysCreator->getKeyHash($repository);
        $this->redis->hincrby($hash, $this->keysCreator->getKeyTotal(), 1);

        $key = $this->keysCreator->createDailyKey();
        $this->redis->hincrby($hash, $key, 1);

        $key = $this->keysCreator->createMonthlyKey();
        $this->redis->hincrby($hash, $key, 1);

        $key = $this->keysCreator->createYearlyKey();
        $this->redis->hincrby($hash, $key, 1);

        return $this;
    }

    /**
     * Increment by one the repository accesses type.
     *
     * @param string $repository
     * @param string $type
     *
     * @return PersisterInterface
     */
    public function incrementRepositoryAccessType($repository, $type)
    {
        $hash = $this->keysCreator->getKeyHash($repository);
        $this->redis->hincrby($hash, $type, 1);

        return $this;
    }

    /**
     * Add the repository to an ordered set of the latest accessed, with unique key value.
     *
     * @param string $repository
     * @param int    $maxListLength
     *
     * @return PersisterInterface
     */
    public function addRepositoryToLatestAccessed($repository, $maxListLength = 50)
    {
        $this->redis->zadd($this->keysCreator->getKeyList(), time(), $repository);

        return $this;
    }

    /**
     * Add the referrer to a subset.
     *
     * @param string $url
     *
     * @return PersisterInterface
     */
    public function addReferer($url)
    {
        $this->redis->zadd($this->keysCreator->getRefererKey(), time() ,$url);

        return $this;
    }


}
