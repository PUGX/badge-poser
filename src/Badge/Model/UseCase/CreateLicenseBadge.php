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
 * Create the 'license' image using a generator `Poser`
 * @package App\Badge\Model\UseCase
 */
class CreateLicenseBadge extends BaseCreatePackagistImage
{
    private CONST COLOR = '428F7E';
    private CONST SUBJECT = 'license';
    private CONST TEXT_NO_LICENSE = 'no';

    /**
     * @param string $repository
     * @param string $format
     *
     * @return Badge
     * @throws InvalidArgumentException
     */
    public function createLicenseBadge(string $repository, string $format = 'svg'): Badge
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT, self::COLOR, $format);
    }

    /**
     * @param Package $package
     * @param null|string $context
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
