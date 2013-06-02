<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests\Controller;

use PUGX\BadgeBundle\Service\ImageCreator;

class FakeImageCreator extends ImageCreator
{
    public function streamRawImageData($image)
    {
        // pass
       return true;
    }
    public function destroyImage($image)
    {
        // pass
        return true;
    }
}
