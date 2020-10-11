<?php

namespace App\Tests\Stats\Persister;

use App\Stats\Persister\RedisPersister;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Predis\Client as Redis;

class RedisPersisterTest extends TestCase
{
    /** @var Redis|MockObject */
    private $redis;

    protected function setUp(): void
    {
        $this->redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testIncrementTotalAccess(): void
    {
        $now = new \DateTime();

        $this->redis
            ->expects($this->at(0))
            ->method('__call')
            ->with('incr', ['STAT.TOTAL']);

        $this->redis
            ->expects($this->at(1))
            ->method('__call')
            ->with('incr', [\sprintf('STAT.TOTAL_%s', $now->format('Y_m_d'))]);
        $this->redis
            ->expects($this->at(2))
            ->method('__call')
            ->with('incr', [\sprintf('STAT.TOTAL_%s', $now->format('Y_m'))]);

        $this->redis
            ->expects($this->at(3))
            ->method('__call')
            ->with('incr', [\sprintf('STAT.TOTAL_%s', $now->format('Y'))]);

        $persister = new RedisPersister($this->redis);

        $persister->incrementTotalAccess();
    }

    public function testIncrementTotalAccessWithCustomKeys(): void
    {
        $now = new \DateTime();

        $this->redis
            ->expects($this->at(0))
            ->method('__call')
            ->with('incr', ['CUSTOM.PREFIX.CUSTOM.TOTAL']);
        $this->redis
            ->expects($this->at(1))
            ->method('__call')
            ->with('incr', [\sprintf('CUSTOM.PREFIX.CUSTOM.TOTAL_%s', $now->format('Y_m_d'))]);
        $this->redis
            ->expects($this->at(2))
            ->method('__call')
            ->with('incr', [\sprintf('CUSTOM.PREFIX.CUSTOM.TOTAL_%s', $now->format('Y_m'))]);

        $this->redis
            ->expects($this->at(3))
            ->method('__call')
            ->with('incr', [\sprintf('CUSTOM.PREFIX.CUSTOM.TOTAL_%s', $now->format('Y'))]);

        $persister = new RedisPersister($this->redis, 'CUSTOM.TOTAL', 'CUSTOM.PREFIX');

        $persister->incrementTotalAccess();
    }

    public function testIncrementRepositoryAccess(): void
    {
        $now = new \DateTime();
        $repoName = 'repoName';

        $this->redis
            ->expects($this->at(0))
            ->method('__call')
            ->with('hincrby', [
                \sprintf('STAT.REPO.%s', $repoName),
                'TOTAL',
                1,
            ]);

        $this->redis
            ->expects($this->at(1))
            ->method('__call')
            ->with('hincrby', [
                \sprintf('STAT.REPO.%s', $repoName),
                \sprintf('TOTAL_%s', $now->format('Y_m_d')),
                1,
            ]);

        $this->redis
            ->expects($this->at(2))
            ->method('__call')
            ->with('hincrby', [
                \sprintf('STAT.REPO.%s', $repoName),
                \sprintf('TOTAL_%s', $now->format('Y_m')),
                1,
            ]);

        $this->redis
            ->expects($this->at(3))
            ->method('__call')
            ->with('hincrby', [
                \sprintf('STAT.REPO.%s', $repoName),
                \sprintf('TOTAL_%s', $now->format('Y')),
                1,
            ]);

        $persister = new RedisPersister($this->redis);

        $persister->incrementRepositoryAccess($repoName);
    }

    public function testIncrementRepositoryAccessWithCustomKeys(): void
    {
        $now = new \DateTime();
        $repoName = 'repoName';

        $this->redis
            ->expects($this->at(0))
            ->method('__call')
            ->with('hincrby', [
                \sprintf('CUSTOM.PREFIX.CUSTOM.HASH.%s', $repoName),
                'TOTAL',
                1,
            ]);

        $this->redis
            ->expects($this->at(1))
            ->method('__call')
            ->with('hincrby', [
                \sprintf('CUSTOM.PREFIX.CUSTOM.HASH.%s', $repoName),
                \sprintf('TOTAL_%s', $now->format('Y_m_d')),
                1,
            ]);

        $this->redis
            ->expects($this->at(2))
            ->method('__call')
            ->with('hincrby', [
                \sprintf('CUSTOM.PREFIX.CUSTOM.HASH.%s', $repoName),
                \sprintf('TOTAL_%s', $now->format('Y_m')),
                1,
            ]);

        $this->redis
            ->expects($this->at(3))
            ->method('__call')
            ->with('hincrby', [
                \sprintf('CUSTOM.PREFIX.CUSTOM.HASH.%s', $repoName),
                \sprintf('TOTAL_%s', $now->format('Y')),
                1,
            ]);

        $persister = new RedisPersister($this->redis, 'TOTAL', 'CUSTOM.PREFIX', 'CUSTOM.HASH');

        $persister->incrementRepositoryAccess($repoName);
    }

    public function testIncrementRepositoryAccessType(): void
    {
        $repoName = 'repoName';
        $repoType = 'repoType';

        $this->redis
            ->expects($this->once())
            ->method('__call')
            ->with('hincrby', [
                \sprintf('STAT.REPO.%s', $repoName),
                $repoType,
                1,
            ]);

        $persister = new RedisPersister($this->redis);

        $persister->incrementRepositoryAccessType($repoName, $repoType);
    }

    public function testIncrementRepositoryAccessTypeWithCustomKeys(): void
    {
        $repoName = 'repoName';
        $repoType = 'repoType';

        $this->redis
            ->expects($this->once())
            ->method('__call')
            ->with('hincrby', [
                \sprintf('CUSTOM.PREFIX.CUSTOM.HASH.%s', $repoName),
                $repoType,
                1,
            ]);

        $persister = new RedisPersister($this->redis, 'TOTAL', 'CUSTOM.PREFIX', 'CUSTOM.HASH');

        $persister->incrementRepositoryAccessType($repoName, $repoType);
    }

    public function testAddRepositoryToLatestAccessed(): void
    {
        $repoName = 'repoName';

        $this->redis
            ->expects($this->once())
            ->method('__call')
            ->with('zadd', [
                'STAT.LIST',
               [$repoName => \time()],
            ]);

        $persister = new RedisPersister($this->redis);

        $persister->addRepositoryToLatestAccessed($repoName);
    }

    public function testAddRepositoryToLatestAccessedWithCustomKeys(): void
    {
        $repoName = 'repoName';

        $this->redis
            ->expects($this->once())
            ->method('__call')
            ->with('zadd', [
                'CUSTOM.PREFIX.CUSTOM.LIST',
                [$repoName => \time()],
            ]);

        $persister = new RedisPersister($this->redis, 'TOTAL', 'CUSTOM.PREFIX', 'HASH', 'CUSTOM.LIST');

        $persister->addRepositoryToLatestAccessed($repoName);
    }

    public function testAddReferer(): void
    {
        $url = 'url';

        $this->redis
            ->expects($this->once())
            ->method('__call')
            ->with('zadd', [
                'STAT.LIST.REFE',
                [$url => \time()],
            ]);

        $persister = new RedisPersister($this->redis);

        $persister->addReferer($url);
    }

    public function testAddRefererWithCustomKeys(): void
    {
        $url = 'url';

        $this->redis
            ->expects($this->once())
            ->method('__call')
            ->with('zadd', [
                'CUSTOM.PREFIX.CUSTOM.LIST.REFE',
                [$url => \time()],
            ]);

        $persister = new RedisPersister($this->redis, 'TOTAL', 'CUSTOM.PREFIX', 'HASH', 'CUSTOM.LIST');

        $persister->addReferer($url);
    }
}
