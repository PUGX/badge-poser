<?php

namespace App\Tests\Stats\Persister;

use App\Stats\Persister\NullPersister;
use PHPUnit\Framework\TestCase;

class NullPersisterTest extends TestCase
{
    public function testIncrementTotalAccess(): void
    {
        $nullPersister = new NullPersister();
        $nullPersister->incrementTotalAccess();

        $this->assertTrue($nullPersister::$incrementTotalAccessCalled);
    }

    public function testIncrementRepositoryAccess(): void
    {
        $repo = 'repo';

        $nullPersister = new NullPersister();
        $nullPersister->incrementRepositoryAccess($repo);

        $this->assertEquals($repo, $nullPersister::$incrementRepositoryAccessCalled);
    }

    public function testIncrementRepositoryAccessType(): void
    {
        $repo = 'repo';
        $type = 'type';

        $nullPersister = new NullPersister();
        $nullPersister->incrementRepositoryAccessType($repo, $type);

        $this->assertEquals([$repo, $type], $nullPersister::$incrementRepositoryAccessTypeCalled);
    }

    public function testAddRepositoryToLatestAccessed(): void
    {
        $repo = 'repo';

        $nullPersister = new NullPersister();
        $nullPersister->addRepositoryToLatestAccessed($repo);

        $this->assertEquals($repo, $nullPersister::$addRepositoryToLatestAccessedCalled);
    }

    public function testAddReferer(): void
    {
        $url = 'url';

        $nullPersister = new NullPersister();
        $nullPersister->addReferer($url);

        $this->assertEquals($url, $nullPersister::$addReferrer);
    }
}
