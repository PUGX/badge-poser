<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Service;

use Imagine\Image\ImageInterface;

/**
 * Class ImageCreatorInterface
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
Interface ImageCreatorInterface
{
    CONST DOWNLOADS = 'downloads';
    CONST STABLE = 'stable';
    CONST UNSTABLE = 'unstable';
    CONST LICENSE = 'license';
    CONST ERROR = 'error';

    /**
     * Create the 'downloads' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return mixed
     */
    public function createDownloadsImage($value);

    /**
     * Create the 'stable:no release' image with the standard Font and stable image template.
     *
     * @param string $value
     *
     * @return ImageInterface
     */
    public function createStableNoImage($value);

    /**
     * Create the 'stable' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createStableImage($value);

    /**
     * Create the 'stable' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createUnstableImage($value = '@dev');

    /**
     * Create the 'error' image
     *
     * @param string $value
     *
     * @return ImageInterface
     */
    public function createErrorImage($value);

    /**
     * Create a 'license' Image
     *
     * @param string $value
     *
     * @return ImageInterface
     */
    public function createLicenseImage($value);
}
