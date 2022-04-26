<?php

declare(strict_types=1);

namespace App\Dictionary;

final class AllInBadges
{
    private const ALL_IN_BADGES = [
        'latest_stable_version',
        'total',
        'latest_unstable_version',
        'license',
        'require_php',
    ];

    public static function isABadgeName(string $badge): bool
    {
        return \in_array($badge, self::ALL_IN_BADGES, true);
    }
}
