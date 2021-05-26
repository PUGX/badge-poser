<?php

namespace App\Event;

use App\Badge\Model\BadgeInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class BadgeEvent extends Event
{
    protected array $data;

    public function __construct(BadgeInterface $badge)
    {
        $this->data = [
            'subject' => $badge->getSubject(),
            'status' => $badge->getStatus(),
            'color' => $badge->getHexColor(),
            'format' => $badge->getFormat(),
        ];
    }

    public function getData(): array
    {
        return $this->data;
    }
}
