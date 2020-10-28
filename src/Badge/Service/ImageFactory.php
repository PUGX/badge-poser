<?php

namespace App\Badge\Service;

use App\Badge\Model\BadgeInterface;
use App\Badge\Model\Image;
use PUGX\Poser\Poser;

/**
 * Class ImageFactory.
 */
class ImageFactory
{
    private Poser $generator;

    public function __construct(Poser $generator)
    {
        $this->generator = $generator;
    }

    public function createFromBadge(BadgeInterface $badge): Image
    {
        $content = $this->generator->generate(
            $badge->getSubject(),
            $badge->getStatus(),
            \trim($badge->getHexColor(), '#'),
            \PUGX\Poser\Badge::DEFAULT_STYLE,
            $badge->getFormat()
        );

        return Image::create((string) $badge, $content, $badge->getFormat());
    }
}
