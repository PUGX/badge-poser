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
 * Class RedisStats
 *
 * @author Simone Fumagalli <simone@iliveinperego.com>
 */
class RedisReader implements ReaderInterface
{
    const KEY_PREFIX = 'STAT';
    const KEY_TOTAL = 'TOTAL';

    private $redis;
    private $keyTotal;
    private $keyPrefix;

    public function __construct($redis, $keyTotal = self::KEY_TOTAL, $keyPrefix = self::KEY_PREFIX)
    {
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
        $this->keyTotal = $this->concatenateKeys($keyPrefix, $keyTotal);
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
     * Read total accesses.
     *
     * @return integer
     */
    public function totalAccess()
    {
        return $this->redis->get($this->keyTotal);
    }

}