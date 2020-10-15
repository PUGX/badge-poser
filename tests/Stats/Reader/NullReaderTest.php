<?php

namespace App\Tests\Stats\Reader;

use App\Stats\Reader\NullReader;
use PHPUnit\Framework\TestCase;

class NullReaderTest extends TestCase
{
    public function testItReadsTotalAccess(): void
    {
        $reader = new NullReader();

        $this->assertEquals(NullReader::$totalAccess, $reader->totalAccess());
    }
}
