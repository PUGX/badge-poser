<?php

declare(strict_types=1);

namespace App\Dictionary;

final class Badges
{
    private const array BADGES = [
        [
            'name' => 'latest_stable_version',
            'label' => 'Latest Stable Version',
            'route' => 'pugx_badge_version_latest',
            'latest' => 'stable',
        ],
        [
            'name' => 'total',
            'label' => 'Total Downloads',
            'route' => 'pugx_badge_download',
        ],
        [
            'name' => 'latest_unstable_version',
            'label' => 'Latest Unstable Version',
            'route' => 'pugx_badge_version_latest',
            'latest' => 'unstable',
        ],
        [
            'name' => 'license',
            'label' => 'License',
            'route' => 'pugx_badge_license',
        ],
        [
            'name' => 'monthly',
            'label' => 'Monthly Downloads',
            'route' => 'pugx_badge_download_type',
            'type' => 'monthly',
        ],
        [
            'name' => 'daily',
            'label' => 'Daily Downloads',
            'route' => 'pugx_badge_download_type',
            'type' => 'daily',
        ],
        [
            'name' => 'version',
            'label' => 'Version',
            'route' => 'pugx_badge_version',
        ],
        [
            'name' => 'require_php',
            'label' => 'PHP Version Require',
            'route' => 'pugx_badge_require',
            'type' => 'php',
        ],
        [
            'name' => 'composerlock',
            'label' => 'composer.lock',
            'route' => 'pugx_badge_composerlock',
        ],
        [
            'name' => 'gitattributes',
            'label' => '.gitattributes',
            'route' => 'pugx_badge_gitattributes',
        ],
        [
            'name' => 'dependents',
            'label' => 'Dependents',
            'route' => 'pugx_badge_dependents',
        ],
        [
            'name' => 'suggesters',
            'label' => 'Suggesters',
            'route' => 'pugx_badge_suggesters',
        ],
        [
            'name' => 'circleci',
            'label' => 'CircleCI Build',
            'route' => 'pugx_badge_circleci',
        ],
    ];

    /**
     * @return array<int, array<string, string>>
     */
    public static function getAll(): array
    {
        return self::BADGES;
    }
}
