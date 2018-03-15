<?php

namespace App\Tests\Contributors\Model;

use App\Contributors\Model\Contributor;
use PHPUnit\Framework\TestCase;

/**
 * Class ContributorTest.
 */
class ContributorTest extends TestCase
{
    public function testCreation(): void
    {
        $contributor = Contributor::create('JellyBellyDev', 'http://profileUrl', 'http://profileImg');

        $this->assertInstanceOf(Contributor::class, $contributor);
        $this->assertEquals('JellyBellyDev', $contributor->getUsername());
        $this->assertEquals('http://profileUrl', $contributor->getProfileUrl());
        $this->assertEquals('http://profileImg', $contributor->getProfileImg());
    }

    public function testToString(): void
    {
        $contributor = Contributor::create('JellyBellyDev', 'http://profileUrl', 'http://profileImg');

        $this->assertEquals('JellyBellyDev', (string) $contributor);
    }
}
