<?php

namespace App\Tests\Contributors\Model;

use App\Contributors\Model\Contributor;
use PHPUnit\Framework\TestCase;

final class ContributorTest extends TestCase
{
    public function testCreation(): void
    {
        $contributor = Contributor::create('JellyBellyDev', 'http://profileUrl', 'http://profileImg');

        self::assertInstanceOf(Contributor::class, $contributor);
        self::assertEquals('JellyBellyDev', $contributor->getUsername());
        self::assertEquals('http://profileUrl', $contributor->getProfileUrl());
        self::assertEquals('http://profileImg?s=160', $contributor->getProfileImg());
    }

    public function testToString(): void
    {
        $contributor = Contributor::create('JellyBellyDev', 'http://profileUrl', 'http://profileImg');

        self::assertEquals('JellyBellyDev', (string) $contributor);
    }
}
