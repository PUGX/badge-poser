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

/**
 * Create the 'license' image using a generator `Poser`
 */
class CreateLicenseBadge extends BaseCreatePackagistImage
{
    CONST COLOR = '428F7E';
    CONST SUBJECT = 'license';
    CONST TEXT_NO_LICENSE = 'no';

    /**
     * @param string $repository
     * @param string $format
     *
     * @return \App\Badge\Model\Badge
     */
    public function createLicenseBadge($repository, $format = 'svg')
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT, self::COLOR, $format);
    }

    protected function prepareText($package, $context = null)
    {
        $license = $package->getLicense();
        if (empty($license)) {
            return self::TEXT_NO_LICENSE;
        }

        return $license;
    }
}
