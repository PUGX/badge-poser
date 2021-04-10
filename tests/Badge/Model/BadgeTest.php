<?php

namespace App\Tests\Badge\Model;

use App\Badge\Model\Badge;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BadgeTest extends TestCase
{
    public function testCreation(): void
    {
        $badge = new Badge('sub', 'status', 'FFFFFF', 'svg');

        self::assertEquals('sub-status-FFFFFF.svg', (string) $badge);
    }

    public function testDashesAndUnderscores(): void
    {
        $badge = new Badge('su--b', 'st-a_tu__s', 'FFFFFF', 'svg');

        self::assertEquals('su-b-st-a tu_s-FFFFFF.svg', (string) $badge);
    }

    public function testShouldUseDefaultImageFormat(): void
    {
        $badge = new Badge('sub', 'status', 'FFFFFF');

        self::assertEquals('svg', $badge->getFormat());
    }

    /** @dataProvider  invalidColorProvider */
    public function testShouldThrowExceptionForInvalidColors(string $color): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Color not valid %s', $color));

        new Badge('sub', 'status', $color);
    }

    /**
     * @return \Generator<array<string>>
     */
    public function invalidColorProvider(): \Generator
    {
        yield [''];
        yield ['null'];
        yield ['#0c0d0'];
        yield ['#0z0z0eZ'];
        yield ['#0c0d0.'];
    }
}
