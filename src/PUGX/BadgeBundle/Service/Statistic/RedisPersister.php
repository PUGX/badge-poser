<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Service\Statistic;

class RedisPersister implements PersisterInterface
{
    const KEY_PREFIX = 'STAT';
    const KEY_TOTAL = 'TOTAL';
    const KEY_HASH_NAME = 'REPO';
    const KEY_LIST_NAME = 'LIST';

    private $redis;
    private $keyTotal;
    private $keyPrefix;
    private $keyHash;
    private $keyList;

    public function __construct($redis, $keyTotal = self::KEY_TOTAL, $keyPrefix = self::KEY_PREFIX, $keyHash = self::KEY_HASH_NAME, $keyList = self::KEY_LIST_NAME)
    {
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
        $this->keyTotal = $this->concatenateKeys($keyPrefix, $keyTotal);
        $this->keyHash = $this->concatenateKeys($keyPrefix, $keyHash);
        $this->keyList = $this->concatenateKeys($keyPrefix, $keyList);
    }

    /**
     * Generate the Key with the default prefix.
     *
     * @param string $prefix
     * @param string $keyName
     *
     * @return string
     */
    private function concatenateKeys($prefix, $keyName)
    {
        return sprintf("%s.%s", $prefix, $keyName);
    }

    /**
     * Increment by one the total accesses.
     *
     * @return PersisterInterface
     */
    public function incrementTotalAccess()
    {
        $this->redis->incr($this->keyTotal);
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
        $this->redis->hincrby($this->concatenateKeys($this->keyHash, $repository), self::KEY_TOTAL, 1);

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
        $this->redis->hincrby($this->concatenateKeys($this->keyHash, $repository), $type, 1);

        return $this;
    }

    /**
     * Add the repository to the list of the latest accessed.
     *
     * @param string $repository
     * @param int $maxListLength
     *
     * @return PersisterInterface
     */
    public function addRepositoryToLatestAccessed($repository, $maxListLength = 50)
    {
        $this->redis->lpush($this->keyList, $repository);

        return $this;
    }
}