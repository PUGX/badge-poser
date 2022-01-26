<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Badge\Model\UseCase;

use App\Badge\Model\Badge;
use App\Badge\Model\CacheableBadge;
use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;

/**
 * Create the 'require' image with the standard Font and standard Image.
 */
final class CreateRequireBadge extends BaseCreatePackagistImage
{
    public const COLOR = '787CB5';
    public const TEXT_NO_REQUIRE = ' - ';

    private const TTL_DEFAULT_MAXAGE = CacheableBadge::TTL_ONE_HOUR;
    private const TTL_DEFAULT_SMAXAGE = CacheableBadge::TTL_ONE_HOUR;

    public function __construct(
        PackageRepositoryInterface $packageRepository
    ) {
        parent::__construct($packageRepository);
    }

    public function createRequireBadge(string $repository, string $type, string $format = Badge::DEFAULT_FORMAT, string $style = Badge::DEFAULT_STYLE): CacheableBadge
    {
        return $this->createBadgeFromRepository(
            $repository,
            $type,
            self::COLOR,
            $format,
            $style,
            $type,
            self::TTL_DEFAULT_MAXAGE,
            self::TTL_DEFAULT_SMAXAGE
        );
    }

    protected function prepareText(Package $package, ?string $context): string
    {
        $require = $package->getLatestRequire((string) $context);

        return empty($require) ? self::TEXT_NO_REQUIRE : $require;
    }
}
