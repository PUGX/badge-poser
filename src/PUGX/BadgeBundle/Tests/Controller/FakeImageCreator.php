<?php

namespace PUGX\BadgeBundle\Tests\Controller;

use Symfony\Bridge\Monolog\Logger;
use PUGX\BadgeBundle\Event\PackageEvent;
use PUGX\BadgeBundle\Service\ImageCreator;


class FakeImageCreator extends ImageCreator
{
    /**
     * This function just do nothing, instead of stream data to the output.
     *
     * @param $image
     */
    public function streamRawImageData($image) {
        // pass
       return true;
    }
    public function destroyImage($image) {
        // pass
        return true;
    }
}