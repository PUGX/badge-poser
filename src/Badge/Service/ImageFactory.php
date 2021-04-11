<?php

namespace App\Badge\Service;

use App\Badge\Model\BadgeInterface;
use App\Badge\Model\Image;
use PUGX\Poser\Poser;

/**
 * Class ImageFactory.
 */
final class ImageFactory
{
    public function __construct(private Poser $generator)
    {
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
