<?php

declare(strict_types=1);

namespace App\Tests\Dictionary;

use App\Dictionary\AllInBadges;
use PHPUnit\Framework\TestCase;

final class AllInBadgesTest extends TestCase
{
    public function testIsABadge(): void
    {
        $badgeName = 'latest_stable_version';

        $this->assertTrue(AllInBadges::isABadgeName($badgeName));
    }

    public function testIsNotABadge(): void
    {
        $badgeName = 'not_a_badge_name';

        $this->assertFalse(AllInBadges::isABadgeName($badgeName));
    }
}
