<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Service;

use Doctrine\Common\Cache;
use Packagist\Api\Client;

/**
 * Class CachedClient, caching layer to the Packagist/Api/Client
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CachedClient extends Client
{
    protected $cache = null;
    protected $ttl_search = 3600;
    protected $ttl_get = 3600;
    protected $ttl_all= 3600;

    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    public function getCache()
    {
        return $this->cache;
    }

    /**
     * return a Key for the cache.
     *
     * @param string $method
     * @param mixed  $argument
     *
     * @return string
     */
    private function getPrefixKey($method, $argument) {
        return sprintf("%s.%s", $method, json_encode($argument));
    }

    public function search($query)
    {
        $key = $this->getPrefixKey(__METHOD__, $query);

        if ($this->getCache()->contains($key)) {
            $results = $this->getCache()->fetch($key);
        } else {
            $results = parent::search($query);
            $this->getCache()->save($key, $results, $this->ttl_search);
        }
        return $results;
    }

    public function get($package)
    {
        $key = $this->getPrefixKey(__METHOD__, $package);

        if ($this->getCache()->contains($key)) {
            $result = $this->getCache()->fetch($key);
        } else {
            $result = parent::get($package);
            $this->getCache()->save($key, $result, $this->ttl_get);
        }

        return $result;
    }

    public function all(array $filters = array())
    {
        $key = $this->getPrefixKey(__METHOD__, $filters);

        if ($this->getCache()->contains($key)) {
            $results = $this->getCache()->fetch($key);
        } else {
            $results = parent::all($filters);
            $this->getCache()->save($key, $results, $this->ttl_all);
        }

        return $results;
    }
}	