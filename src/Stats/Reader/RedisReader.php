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

    /** @var Redis<string, Redis> */
    private Redis $redis;
    private string $keyTotal;

    /** @param Redis<string, Redis> $redis */
    public function __construct(
        Redis $redis,
        string $keyTotal = self::KEY_TOTAL,
        string $keyPrefix = self::KEY_PREFIX
    ) {
        $this->redis = $redis;
        $this->keyTotal = $this->concatenateKeys($keyPrefix, $keyTotal);
    }

    private function concatenateKeys(string $prefix, string $keyName): string
    {
        return \sprintf('%s.%s', $prefix, $keyName);
    }

    public function totalAccess(): int
    {
        return (int) $this->redis->get($this->keyTotal);
    }
}
