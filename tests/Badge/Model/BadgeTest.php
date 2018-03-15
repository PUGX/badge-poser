<?php

namespace App\Tests\Badge\Model;

use App\Badge\Model\Badge;
use PHPUnit\Framework\TestCase;

/**
 * Class BadgeTest.
 */
class BadgeTest extends TestCase
{
    public function testCreation(): void
    {
        $badge = new Badge('sub', 'status', 'FFFFFF', 'svg');

        $this->assertEquals('sub-status-FFFFFF.svg', (string) $badge);
    }

    public function testDashesAndUnderscores(): void
    {
        $badge = new Badge('su--b', 'st-a_tu__s', 'FFFFFF', 'svg');

        $this->assertEquals('su-b-st-a tu_s-FFFFFF.svg', (string) $badge);
    }
}
