<?php

namespace App\Badge\Service;

use App\Badge\Model\Badge;
use App\Poser\Poser;
use App\Badge\Model\Image;

class ImageFactory
{
    /**
     * @var Poser $generator
     */
    private $generator;

    public function __construct(Poser $generator)
    {
        $this->generator = $generator;
    }

    public function createFromBadge(Badge $badge)
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
