<?php

namespace App\Tests\Stats\Reader;

use App\Stats\Reader\RedisReader;
use PHPUnit\Framework\TestCase;
use Predis\Client as Redis;

final class RedisReaderTest extends TestCase
{
    public function testItReadsTotalAccessWithIntValue(): void
    {
        $redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();

        $redis->method('__call')
            ->with('get', [\sprintf('%s.%s', 'STAT', 'TOTAL')])
            ->willReturn(10);

        $reader = new RedisReader($redis);

        self::assertEquals(10, $reader->totalAccess());
    }

    public function testItReadsTotalAccessWithNumericString(): void
    {
        $redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();

        $redis->method('__call')
            ->with('get', [\sprintf('%s.%s', 'STAT', 'TOTAL')])
            ->willReturn('10');

        $reader = new RedisReader($redis);

        self::assertEquals(10, $reader->totalAccess());
    }

    public function testItReadsTotalAccessWithNullValue(): void
    {
        $redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();

        $redis->method('__call')
            ->with('get', [\sprintf('%s.%s', 'STAT', 'TOTAL')])
            ->willReturn(null);

        $reader = new RedisReader($redis);

        self::assertEquals(0, $reader->totalAccess());
    }

    public function testItReadsTotalAccessWithStringValue(): void
    {
        $redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();

        $redis->method('__call')
            ->with('get', [\sprintf('%s.%s', 'STAT', 'TOTAL')])
            ->willReturn('aString');

        $reader = new RedisReader($redis);

        self::assertEquals(0, $reader->totalAccess());
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

        self::assertEquals(10, $reader->totalAccess());
    }
}
