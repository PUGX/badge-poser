<?php

declare(strict_types=1);

namespace App\Badge\Service;

use App\Badge\Model\BadgeInterface;
use App\Badge\Model\Image;

interface ImageFactoryInterface
{
    public function createFromBadge(BadgeInterface $badge): Image;
}
