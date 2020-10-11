<?php

namespace App\Stats\Persister;

use Predis\Client as Redis;

/**
 * Class RedisPersister.
 */
final class RedisPersister implements PersisterInterface
{
    private const KEY_PREFIX = 'STAT';
    private const KEY_TOTAL = 'TOTAL';
    private const KEY_HASH_NAME = 'REPO';
    private const KEY_LIST_NAME = 'LIST';
    private const KEY_REFERER_SUFFIX = 'REFE';

    /** @var Redis<string, Redis> */
    private Redis $redis;
    private string $keyTotal;
    private string $keyPrefix;
    private string $keyHash;
    private string $keyList;

    /** @param Redis<string, Redis> $redis */
    /*
     * @todo done in this way, custom args are never used if we want to customize only one of those
     * (passing null is not feaseable, passing en empty string would result in empty string)
     * Maybe is better to use ?string as typehint?
     */
    public function __construct(
        Redis $redis,
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
        $this->redis->hincrby($this->concatenateKeys($this->keyHash, $repository), self::KEY_TOTAL, 1);

        $key = $this->createDailyKey(self::KEY_TOTAL);
        $this->redis->hincrby($this->concatenateKeys($this->keyHash, $repository), $key, 1);

        $key = $this->createMonthlyKey(self::KEY_TOTAL);
        $this->redis->hincrby($this->concatenateKeys($this->keyHash, $repository), $key, 1);

        $key = $this->createYearlyKey(self::KEY_TOTAL);
        $this->redis->hincrby($this->concatenateKeys($this->keyHash, $repository), $key, 1);

        return $this;
    }

    public function incrementRepositoryAccessType(string $repository, string $type): PersisterInterface
    {
        $this->redis->hincrby($this->concatenateKeys($this->keyHash, $repository), $type, 1);

        return $this;
    }

    // @todo $maxListLength seems to be useless, remove in next major version?
    public function addRepositoryToLatestAccessed(string $repository, int $maxListLength = 50): PersisterInterface
    {
        $this->redis->zadd($this->keyList, [$repository => \time()]);

        return $this;
    }

    public function addReferer(string $url): PersisterInterface
    {
        $this->redis->zadd($this->concatenateKeys($this->keyList, self::KEY_REFERER_SUFFIX), [$url => \time()]);

        return $this;
    }

    /**
     * Generate the Key with the default prefix.
     */
    private function concatenateKeys(string $prefix, string $keyName): string
    {
        return \sprintf('%s.%s', $prefix, $keyName);
    }

    /**
     * Create the yearly key with prefix eg. 'total_2003'.
     */
    private function createYearlyKey(string $prefix, \DateTime $datetime = null): string
    {
        return \sprintf('%s_%s', $prefix, $this->formatDate($datetime, 'Y'));
    }

    /**
     * Create the monthly key with prefix eg. 'total_2003_11'.
     */
    private function createMonthlyKey(string $prefix, \DateTime $datetime = null): string
    {
        return \sprintf('%s_%s', $prefix, $this->formatDate($datetime, 'Y_m'));
    }

    /**
     * Create the daily key with prefix eg. 'total_2003_11_29'.
     */
    private function createDailyKey(string $prefix, \DateTime $datetime = null): string
    {
        return \sprintf('%s_%s', $prefix, $this->formatDate($datetime, 'Y_m_d'));
    }

    /**
     * format a date.
     */
    private function formatDate(\DateTime $datetime = null, string $format = 'Y_m_d'): string
    {
        /*
         * @todo this condition seems to be always true. There's any reason I'm missing to have $datetime as argument?.
         * Furthermore, is better to have a provider (dependency) for time control, instead of \DateTime and \time directly
         * there (tests can be, somehow, affected and fail due to those circumstances)
         */
        if (null === $datetime) {
            $datetime = new \DateTime('now');
        }

        return $datetime->format($format);
    }
}
