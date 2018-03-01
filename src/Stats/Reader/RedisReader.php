<?php

namespace App\Stats\Reader;

use Predis\Client as Redis;

final class RedisReader implements ReaderInterface
{
    const KEY_PREFIX = 'STAT';
    const KEY_TOTAL = 'TOTAL';

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

    private function concatenateKeys(string $prefix, string $keyName): string
    {
        return sprintf('%s.%s', $prefix, $keyName);
    }

    public function totalAccess(): int
    {
        return $this->redis->get($this->keyTotal);
    }
}
