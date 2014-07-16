<?php

namespace PUGX\Badge\Model;

class BadgeTest extends \PHPUnit_Framework_TestCase
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
