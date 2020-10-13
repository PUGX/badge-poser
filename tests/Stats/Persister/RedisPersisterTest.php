<?php

namespace App\Tests\Stats\Persister;

use App\DateProvider\DateTimeProviderInterface;
use App\Stats\Persister\RedisPersister;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Predis\Client as Redis;

class RedisPersisterTest extends TestCase
{
    /** @var Redis|MockObject */
    private $redis;

    /** @var DateTimeProviderInterface|MockObject */
    private $dateTimeProvider;

    private \DateTimeInterface $currentDateTime;

    protected function setUp(): void
    {
        $this->redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->currentDateTime = new \DateTime();

        $this->dateTimeProvider = $this->getMockBuilder(DateTimeProviderInterface::class)
            ->getMock();
        $this->dateTimeProvider
            ->method('getDateTime')
            ->willReturn($this->currentDateTime);
        $this->dateTimeProvider
            ->method('getTime')
            ->willReturn($this->currentDateTime->getTimestamp());
    }

    public function testIncrementTotalAccess(): void
    {
        $this->redis
            ->expects($this->exactly(4))
            ->method('__call')
            ->withConsecutive(
                ['incr', ['STAT.TOTAL']],
                ['incr', [\sprintf('STAT.TOTAL_%s', $this->currentDateTime->format('Y_m_d'))]],
                ['incr', [\sprintf('STAT.TOTAL_%s', $this->currentDateTime->format('Y_m'))]],
                ['incr', [\sprintf('STAT.TOTAL_%s', $this->currentDateTime->format('Y'))]]
            );

        $persister = new RedisPersister($this->redis, $this->dateTimeProvider);

        $persister->incrementTotalAccess();
    }

    public function testIncrementRepositoryAccess(): void
    {
        $repoName = 'repoName';

        $this->redis
            ->expects($this->exactly(4))
            ->method('__call')
            ->withConsecutive(
                ['hincrby', [\sprintf('STAT.REPO.%s', $repoName), 'TOTAL', 1]],
                ['hincrby', [
                    \sprintf('STAT.REPO.%s', $repoName),
                    \sprintf('TOTAL_%s', $this->currentDateTime->format('Y_m_d')),
                    1,
                ]],
                ['hincrby', [
                    \sprintf('STAT.REPO.%s', $repoName),
                    \sprintf('TOTAL_%s', $this->currentDateTime->format('Y_m')),
                    1,
                ]],
                ['hincrby', [
                    \sprintf('STAT.REPO.%s', $repoName),
                    \sprintf('TOTAL_%s', $this->currentDateTime->format('Y')),
                    1,
                ]],
            );

        $persister = new RedisPersister($this->redis, $this->dateTimeProvider);

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

        $persister = new RedisPersister($this->redis, $this->dateTimeProvider);

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
               [$repoName => $this->currentDateTime->getTimestamp()],
            ]);

        $persister = new RedisPersister($this->redis, $this->dateTimeProvider);

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
                [$url => $this->currentDateTime->getTimestamp()],
            ]);

        $persister = new RedisPersister($this->redis, $this->dateTimeProvider);

        $persister->addReferer($url);
    }
}
