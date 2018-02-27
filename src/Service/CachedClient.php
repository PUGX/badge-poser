<?php

namespace App\Service;

use Doctrine\Common\Cache\Cache;
use Packagist\Api\Client;

class CachedClient
{
    protected $cache = null;
    protected $TTLSearch = 900;
    protected $TTLGet = 900;
    protected $TTLAll= 900;
    protected $client;

    public function __construct(Client $client, Cache $cache, $TTLSearch, $TTLGet, $TTLAll)
    {
        $this->cache = $cache;
        $this->client = $client;
        $this->TTLSearch = $TTLSearch;
        $this->TTLGet = $TTLGet;
        $this->TTLAll = $TTLAll;
    }

    public function getTTLAll()
    {
        return $this->TTLAll;
    }

    public function getTTLGet()
    {
        return $this->TTLGet;
    }

    public function getTTLSearch()
    {
        return $this->TTLSearch;
    }

    public function setResultFactory($resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    public function getCache()
    {
        return $this->cache;
    }

    private function getPrefixKey($method, $argument)
    {
        return sprintf("%s.%s", $method, json_encode($argument));
    }

    public function search($query, array $filters = array())
    {
        $key = $this->getPrefixKey(__METHOD__, $query);

        if ($this->getCache()->contains($key)) {
            $results = $this->getCache()->fetch($key);

            return $results;
        }

        $results = $this->client->search($query, $filters);
        $this->getCache()->save($key, $results, $this->TTLSearch);

        return $results;
    }

    public function get($package)
    {
        $key = $this->getPrefixKey(__METHOD__, $package);

        if ($this->getCache()->contains($key)) {
            $result = $this->getCache()->fetch($key);
        } else {
            $result = $this->client->get($package);
            $this->getCache()->save($key, $result, $this->TTLGet);
        }

        return $result;
    }

    public function all(array $filters = array())
    {
        $key = $this->getPrefixKey(__METHOD__, $filters);

        if ($this->getCache()->contains($key)) {
            $results = $this->getCache()->fetch($key);

            return $results;
        }
        $results = $this->client->all($filters);
        $this->getCache()->save($key, $results, $this->TTLAll);

        return $results;
    }
}
