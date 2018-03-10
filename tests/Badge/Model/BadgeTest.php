<?php

namespace App\Tests\Badge\Model;

use PHPUnit\Framework\TestCase;
use App\Badge\Model\Badge;

/**
 * Class BadgeTest
 * @package App\Tests\Badge\Model
 */
class BadgeTest extends TestCase
{
    public function testCreation()
    {
        $badge = new Badge('sub','status', 'FFFFFF', 'svg');

        $this->assertEquals('sub-status-FFFFFF.svg', (string) $badge);
    }

    public function testDashesAndUnderscores()
    {
        $badge = new Badge('su--b','st-a_tu__s', 'FFFFFF', 'svg');

        $this->assertEquals('su-b-st-a tu_s-FFFFFF.svg', (string) $badge);
    }
}
