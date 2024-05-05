<?php

namespace App\Badge\Service;

use App\Badge\Model\BadgeInterface;
use App\Badge\Model\Image;
use App\Event\BadgeEvent;
use PUGX\Poser\Poser;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class ImageFactory.
 */
final readonly class ImageFactory
{
    public function __construct(
        private Poser $generator,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createFromBadge(BadgeInterface $badge): Image
    {
        $badgeEvent = new BadgeEvent($badge);
        $this->eventDispatcher->dispatch($badgeEvent);

        $content = $this->generator->generate(
            $badge->getSubject(),
            $badge->getStatus(),
            \trim($badge->getHexColor(), '#'),
            $badge->getStyle(),
            $badge->getFormat()
        );

        return Image::create((string) $badge, $content, $badge->getFormat());
    }
}
