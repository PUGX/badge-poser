<?php

namespace App\Badge\Service;

use App\Badge\Model\Badge;
use App\Badge\Model\Image;
use PUGX\Poser\Poser;

/**
 * Class ImageFactory.
 */
class ImageFactory
{
    /**
     * @var Poser
     */
    private $generator;

    public function __construct(Poser $generator)
    {
        $this->generator = $generator;
    }

    public function createFromBadge(Badge $badge): Image
    {
        $content = $this->generator->generate(
            $badge->getSubject(),
            $badge->getStatus(),
            trim($badge->getHexColor(), '#'),
            $badge->getFormat()
        );

        return Image::create((string) $badge, $content, $badge->getFormat());
    }
}
