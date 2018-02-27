<?php

namespace App\Service;

use Predis\Client;

/**
 * Class RedisReader
 * @package App\Service
 *
 * @author Roberto Umbertini <daeronmalnwe@gmail.com>
 */
class RedisReader implements ReaderInterface
{
    const KEY_PREFIX = 'STAT';
    const KEY_TOTAL = 'TOTAL';

    /**
     * @var string
     */
    protected $keyTotal;
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->keyTotal = $this->concatenateKeys(self::KEY_PREFIX, self::KEY_TOTAL);
    }

    public function totalAccess()
    {
        return $this->client->get($this->keyTotal);
    }

    public function concatenateKeys($prefix, $keyName): string
    {
        return sprintf('%s.%s', $prefix, $keyName);
    }
}