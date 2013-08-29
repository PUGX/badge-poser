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

use Packagist\Api\Client;

/**
 * Class CachedClient, caching layer to the Packagist/Api/Client
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CachedClient extends Client
{
    protected $cache = null;
    protected $TTLSearch = 1800;
    protected $TTLGet = 1800;
    protected $TTLAll= 1800;

    /**
     * @param int $TTLAll
     */
    public function setTTLAll($TTLAll)
    {
        $this->TTLAll = $TTLAll;
    }

    /**
     * @return int
     */
    public function getTTLAll()
    {
        return $this->TTLAll;
    }

    /**
     * @param int $TTLGet
     */
    public function setTTLGet($TTLGet)
    {
        $this->TTLGet = $TTLGet;
    }

    /**
     * @return int
     */
    public function getTTLGet()
    {
        return $this->TTLGet;
    }

    /**
     * @param int $TTLSearch
     */
    public function setTTLSearch($TTLSearch)
    {
        $this->TTLSearch = $TTLSearch;
    }

    /**
     * @return int
     */
    public function getTTLSearch()
    {
        return $this->TTLSearch;
    }

    /**
     * @param \Guzzle\Http\ClientInterface $httpClient
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return \Guzzle\Http\ClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param \Packagist\Api\Result\Factory $resultFactory
     */
    public function setResultFactory($resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return \Packagist\Api\Result\Factory
     */
    public function getResultFactory()
    {
        return $this->resultFactory;
    }

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
            $this->getCache()->save($key, $results, $this->TTLSearch);
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
            $this->getCache()->save($key, $result, $this->TTLGet);
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
            $this->getCache()->save($key, $results, $this->TTLAll);
        }

        return $results;
    }
}	