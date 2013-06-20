<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests\Controller;

use PUGX\BadgeBundle\Service\ImageCreator;
use Imagine\Image\ImageInterface;

class FakeImageCreator extends ImageCreator
{
    public function streamRawImageData(ImageInterface $image)
    {
        // pass
       return true;
    }
}
