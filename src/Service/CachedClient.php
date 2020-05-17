<?php

namespace App\Service;

use GuzzleHttp\ClientInterface;
use Packagist\Api\Client;
use Predis\Profile\Factory;

/**
 * Class CachedClient.
 */
class CachedClient extends Client
{
    protected $httpClient;

    protected $cache;

    /**
     * @var Factory
     */
    protected $resultFactory;

    protected int $TTLSearch = 900;

    protected int $TTLGet = 900;

    protected int $TTLAll = 900;

    public function setTTLAll(int $TTLAll): void
    {
        $this->TTLAll = $TTLAll;
    }

    public function getTTLAll(): int
    {
        return $this->TTLAll;
    }

    public function setTTLGet(int $TTLGet): void
    {
        $this->TTLGet = $TTLGet;
    }

    public function getTTLGet(): int
    {
        return $this->TTLGet;
    }

    public function setTTLSearch(int $TTLSearch): void
    {
        $this->TTLSearch = $TTLSearch;
    }

    public function getTTLSearch(): int
    {
        return $this->TTLSearch;
    }

    public function setHttpClient(ClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    public function setResultFactory(Factory $resultFactory): void
    {
        $this->resultFactory = $resultFactory;
    }

    public function getResultFactory(): Factory
    {
        return $this->resultFactory;
    }

    public function setCache($cache): void
    {
        $this->cache = $cache;
    }

    public function getCache()
    {
        return $this->cache;
    }

    /**
     * return a Key for the cache.
     */
    private function getPrefixKey(string $method, $argument): string
    {
        return \sprintf('%s.%s', $method, \json_encode($argument));
    }

    public function search($query, array $filters = [])
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

    public function all(array $filters = [])
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
