<?php

namespace App\Stats\Persister;

final class RedisPersister implements PersisterInterface
{
    const KEY_PREFIX = 'STAT';
    const KEY_TOTAL = 'TOTAL';
    const KEY_HASH_NAME = 'REPO';
    const KEY_LIST_NAME = 'LIST';
    const KEY_REFERER_SUFFIX = 'REFE';

    private $redis;
    private $keyTotal;
    private $keyPrefix;
    private $keyHash;
    private $keyList;

    public function __construct(
        \Redis $redis,
        string $keyTotal = self::KEY_TOTAL,
        string $keyPrefix = self::KEY_PREFIX,
        string $keyHash = self::KEY_HASH_NAME,
        string $keyList = self::KEY_LIST_NAME
    ) {
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
        $this->keyTotal = $this->concatenateKeys($keyPrefix, $keyTotal);
        $this->keyHash = $this->concatenateKeys($keyPrefix, $keyHash);
        $this->keyList = $this->concatenateKeys($keyPrefix, $keyList);
    }


    public function incrementTotalAccess(): PersisterInterface
    {
        $this->redis->incr($this->keyTotal);

        $key = $this->createDailyKey($this->keyTotal);
        $this->redis->incr($key);

        $key = $this->createMonthlyKey($this->keyTotal);
        $this->redis->incr($key);

        $key = $this->createYearlyKey($this->keyTotal);
        $this->redis->incr($key);

        return $this;
    }

    public function incrementRepositoryAccess(string $repository): PersisterInterface
    {
        $this->redis->hIncrBy($this->concatenateKeys($this->keyHash, $repository), self::KEY_TOTAL, 1);

        $key = $this->createDailyKey(self::KEY_TOTAL);
        $this->redis->hIncrBy($this->concatenateKeys($this->keyHash, $repository), $key, 1);

        $key = $this->createMonthlyKey(self::KEY_TOTAL);
        $this->redis->hIncrBy($this->concatenateKeys($this->keyHash, $repository), $key, 1);

        $key = $this->createYearlyKey(self::KEY_TOTAL);
        $this->redis->hIncrBy($this->concatenateKeys($this->keyHash, $repository), $key, 1);

        return $this;
    }

    public function incrementRepositoryAccessType(string $repository, string $type): PersisterInterface
    {
        $this->redis->hIncrBy($this->concatenateKeys($this->keyHash, $repository), $type, 1);

        return $this;
    }

    public function addRepositoryToLatestAccessed(string $repository, int $maxListLength = 50): PersisterInterface
    {
        $this->redis->zAdd($this->keyList, time() ,$repository);

        return $this;
    }

    public function addReferer(string $url): PersisterInterface
    {
        $this->redis->zAdd($this->concatenateKeys($this->keyList, self::KEY_REFERER_SUFFIX), time() ,$url);

        return $this;
    }

    /**
     * Generate the Key with the default prefix.
     *
     * @param string $prefix
     * @param string $keyName
     *
     * @return string
     */
    private function concatenateKeys(string $prefix, string $keyName): string
    {
        return sprintf('%s.%s', $prefix, $keyName);
    }

    /**
     * Create the yearly key with prefix eg. 'total_2003'
     *
     * @param string    $prefix
     * @param \DateTime $datetime
     *
     * @return string
     */
    private function createYearlyKey(string $prefix, \DateTime $datetime = null): string
    {
        return sprintf('%s_%s', $prefix, $this->formatDate($datetime, 'Y'));
    }

    /**
     * Create the monthly key with prefix eg. 'total_2003_11'
     *
     * @param string    $prefix
     * @param \DateTime $datetime
     *
     * @return string
     */
    private function createMonthlyKey(string $prefix, \DateTime $datetime = null): string
    {
        return sprintf('%s_%s', $prefix, $this->formatDate($datetime, 'Y_m'));
    }

    /**
     * Create the daily key with prefix eg. 'total_2003_11_29'
     *
     * @param string    $prefix
     * @param \DateTime $datetime
     *
     * @return string
     */
    private function createDailyKey(string $prefix, \DateTime $datetime = null): string
    {
        return sprintf('%s_%s', $prefix, $this->formatDate($datetime, 'Y_m_d'));
    }

    /**
     * format a date.
     *
     * @param \DateTime $datetime
     * @param string    $format
     *
     * @return string
     */
    private function formatDate(\DateTime $datetime = null, string $format = 'Y_m_d'): string
    {
        if (null === $datetime) {
            $datetime = new \DateTime('now');
        }

        return $datetime->format($format);
    }
}
