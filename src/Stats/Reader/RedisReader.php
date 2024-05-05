<?php

namespace App\Stats\Reader;

use Predis\Client as Redis;

final class RedisReader implements ReaderInterface
{
    private const string KEY_PREFIX = 'STAT';
    private const string KEY_TOTAL = 'TOTAL';
    private string $keyTotal;

    /**
     * @param Redis<string, Redis> $redis
     */
    public function __construct(
        private readonly Redis $redis,
        string $keyTotal = self::KEY_TOTAL,
        string $keyPrefix = self::KEY_PREFIX,
    ) {
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
