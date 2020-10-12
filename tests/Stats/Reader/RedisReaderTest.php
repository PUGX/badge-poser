<?php

namespace App\Tests\Stats\Reader;

use App\Stats\Reader\RedisReader;
use PHPUnit\Framework\TestCase;
use Predis\Client as Redis;

class RedisReaderTest extends TestCase
{
    /**
     * @dataProvider readsTotalAccess
     */
    public function testItReadsTotalAccess(string $prefixKey, string $totalKey, $total, int $expectedTotal): void
    {
        $redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();

        $redis->method('__call')
            ->with('get', [\sprintf('%s.%s', $prefixKey, $totalKey)])
            ->willReturn($total);

        $reader = new RedisReader($redis);

        $this->assertEquals($expectedTotal, $reader->totalAccess());
    }

    public function readsTotalAccess(): array
    {
        return [
            [
                'STAT', 'TOTAL', 10, 10,
            ],
            [
                'STAT', 'TOTAL', '10', 10,
            ],
            [
                'STAT', 'TOTAL', null, 0,
            ],
            [
                'STAT', 'TOTAL', 'aString', 0,
            ],
        ];
    }

    public function testItReadsTotalAccessWithCustomKeys(): void
    {
        $redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();

        $totalKey = 'TOTAL.CUSTOM';
        $prefixKey = 'PREFIX.CUSTOM';

        $total = 10;

        $redis->method('__call')
            ->with('get', [\sprintf('%s.%s', $totalKey, $prefixKey)])
            ->willReturn($total);

        $reader = new RedisReader($redis, $prefixKey, $totalKey);

        $this->assertEquals(10, $reader->totalAccess());
    }
}
