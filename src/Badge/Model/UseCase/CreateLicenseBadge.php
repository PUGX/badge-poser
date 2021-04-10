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

use App\Badge\Model\CacheableBadge;
use App\Badge\Model\Package;
use InvalidArgumentException;

/**
 * Create the 'license' image using a generator `Poser`.
 */
final class CreateLicenseBadge extends BaseCreatePackagistImage
{
    private const COLOR = '428F7E';
    private const SUBJECT = 'license';
    private const TEXT_NO_LICENSE = 'no';

    private const TTL_DEFAULT_MAXAGE = CacheableBadge::TTL_ONE_HOUR;
    private const TTL_DEFAULT_SMAXAGE = CacheableBadge::TTL_ONE_HOUR;

    /**
     * @throws InvalidArgumentException
     */
    public function createLicenseBadge(string $repository, string $format = 'svg'): CacheableBadge
    {
        return $this->createBadgeFromRepository(
            $repository,
            self::SUBJECT,
            self::COLOR,
            $format,
            null,
            self::TTL_DEFAULT_MAXAGE,
            self::TTL_DEFAULT_SMAXAGE
        );
    }

    protected function prepareText(Package $package, ?string $context): string
    {
        $license = $package->getLicense();
        if (empty($license)) {
            return self::TEXT_NO_LICENSE;
        }

        return $license;
    }
}
