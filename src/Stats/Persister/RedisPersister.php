<?php

namespace App\Stats\Persister;

use App\DateProvider\DateTimeProviderInterface;
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
    private DateTimeProviderInterface $dateTimeProvider;
    private string $keyTotal;
    private string $keyHash;
    private string $keyList;

    /** @param Redis<string, Redis> $redis */
    public function __construct(Redis $redis, DateTimeProviderInterface $dateTimeProvider)
    {
        $this->redis = $redis;
        $this->dateTimeProvider = $dateTimeProvider;
        $this->keyTotal = $this->concatenateKeys(self::KEY_PREFIX, self::KEY_TOTAL);
        $this->keyHash = $this->concatenateKeys(self::KEY_PREFIX, self::KEY_HASH_NAME);
        $this->keyList = $this->concatenateKeys(self::KEY_PREFIX, self::KEY_LIST_NAME);
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

    public function addRepositoryToLatestAccessed(string $repository): PersisterInterface
    {
        $this->redis->zadd($this->keyList, [$repository => $this->dateTimeProvider->getTime()]);

        return $this;
    }

    public function addReferer(string $url): PersisterInterface
    {
        $this->redis->zadd(
            $this->concatenateKeys($this->keyList, self::KEY_REFERER_SUFFIX),
            [$url => $this->dateTimeProvider->getTime()]
        );

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
    private function createYearlyKey(string $prefix): string
    {
        return \sprintf('%s_%s', $prefix, $this->dateTimeProvider->getDateTime()->format('Y'));
    }

    /**
     * Create the monthly key with prefix eg. 'total_2003_11'.
     */
    private function createMonthlyKey(string $prefix): string
    {
        return \sprintf('%s_%s', $prefix, $this->dateTimeProvider->getDateTime()->format('Y_m'));
    }

    /**
     * Create the daily key with prefix eg. 'total_2003_11_29'.
     */
    private function createDailyKey(string $prefix): string
    {
        return \sprintf('%s_%s', $prefix, $this->dateTimeProvider->getDateTime()->format('Y_m_d'));
    }
}
