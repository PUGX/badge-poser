<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Service;

use Symfony\Bridge\Monolog\Logger;

Interface ImageCreatorInterface
{
    /**
     * Stream the output.
     *
     * @param resource $image
     *
     * @return Boolean
     */
    public function streamRawImageData($image);

    /**
     * Destroy the resource.
     *
     * @param $image
     *
     * @return Boolean
     */
    public function destroyImage($image);


    /**
     * Create the 'downloads' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createDownloadsImage($value);

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
}