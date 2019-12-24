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
use App\Badge\Model\Package;
use InvalidArgumentException;

/**
 * Class CreateLicenseBadge
 * Create the 'license' image using a generator `Poser`.
 */
class CreateLicenseBadge extends BaseCreatePackagistImage
{
    private const COLOR = '428F7E';
    private const SUBJECT = 'license';
    private const TEXT_NO_LICENSE = 'no';

    /**
     * @throws InvalidArgumentException
     */
    public function createLicenseBadge(string $repository, string $format = 'svg'): Badge
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT, self::COLOR, $format);
    }

    /**
     * @param string|null $context
     *
     * @return mixed|string
     */
    protected function prepareText(Package $package, $context = null)
    {
        $license = $package->getLicense();
        if (empty($license)) {
            return self::TEXT_NO_LICENSE;
        }

        return $license;
    }
}
