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
Interface ImageStreamerInterface
{
    /**
     * Stream the output.
     *
     * @param ImageInterface $image
     *
     * @return Boolean
     */
    public function streamRawImageData(ImageInterface $image);
}
