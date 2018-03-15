<?php

namespace App\Stats\Reader;

use Predis\Client as Redis;

/**
 * Class RedisReader.
 */
final class RedisReader implements ReaderInterface
{
    private const KEY_PREFIX = 'STAT';
    private const KEY_TOTAL = 'TOTAL';

    private $redis;
    private $keyTotal;
    private $keyPrefix;

    public function __construct(
        Redis $redis,
        string $keyTotal = self::KEY_TOTAL,
        string $keyPrefix = self::KEY_PREFIX
    ) {
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
        $this->keyTotal = $this->concatenateKeys($keyPrefix, $keyTotal);
    }

    /**
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
     * @return int
     */
    public function totalAccess(): int
    {
        return $this->redis->get($this->keyTotal);
    }
}
