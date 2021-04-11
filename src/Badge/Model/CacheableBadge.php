<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Badge\Model;

/**
 * Class CacheableBadge.
 */
final class CacheableBadge implements BadgeInterface, \Stringable
{
    public const TTL_NO_CACHE = 0;
    public const TTL_ONE_HOUR = 60 * 60;
    public const TTL_SIX_HOURS = 6 * self::TTL_ONE_HOUR;
    public const TTL_TWELVE_HOURS = 12 * self::TTL_ONE_HOUR;
    public const TTL_ONE_DAY = 24 * self::TTL_ONE_HOUR;
    public const TTL_ONE_WEEK = 7 * self::TTL_ONE_DAY;

    public function __construct(private BadgeInterface $badge, private int $maxage, private int $smaxage)
    {
    }

    public function getSubject(): string
    {
        return $this->badge->getSubject();
    }

    public function getStatus(): string
    {
        return $this->badge->getStatus();
    }

    public function getHexColor(): string
    {
        return $this->badge->getHexColor();
    }

    public function getFormat(): string
    {
        return $this->badge->getFormat();
    }

    public function getMaxAge(): int
    {
        return $this->maxage;
    }

    public function setMaxAge(int $maxage): void
    {
        $this->maxage = $maxage;
    }

    public function getSMaxAge(): int
    {
        return $this->smaxage;
    }

    public function setSMaxAge(int $smaxage): void
    {
        $this->smaxage = $smaxage;
    }

    public function __toString(): string
    {
        return $this->badge->__toString();
    }
}
