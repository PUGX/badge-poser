<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Badge\Model\UseCase;

use App\Badge\Model\Badge;
use App\Badge\Model\BadgeInterface;
use App\Badge\Model\CacheableBadge;
use GuzzleHttp\Exception\BadResponseException;

/**
 * Create the 'error' badge with the standard Font and standard Image.
 */
final class CreateErrorBadge
{
    private const string COLOR = 'e05d44';
    private const string ERROR_TEXT_GENERIC = 'generic';
    private const string ERROR_TEXT_CLIENT_EXCEPTION = 'connection';
    private const string ERROR_TEXT_CLIENT_BAD_RESPONSE = 'not found?';

    private const int TTL_DEFAULT_MAXAGE = CacheableBadge::TTL_NO_CACHE;
    private const int TTL_DEFAULT_SMAXAGE = CacheableBadge::TTL_NO_CACHE;

    /**
     * @throws \InvalidArgumentException
     */
    public function createErrorBadge(\Throwable $throwable, string $format = Badge::DEFAULT_FORMAT, string $style = Badge::DEFAULT_STYLE): CacheableBadge
    {
        return new CacheableBadge(
            $this->createBadge($throwable, $format, $style),
            self::TTL_DEFAULT_MAXAGE,
            self::TTL_DEFAULT_SMAXAGE
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function createBadge(\Throwable $throwable, string $format = Badge::DEFAULT_FORMAT, string $style = Badge::DEFAULT_STYLE): BadgeInterface
    {
        $status = self::ERROR_TEXT_GENERIC;
        if ($throwable instanceof BadResponseException) {
            $status = self::ERROR_TEXT_CLIENT_BAD_RESPONSE;
        } elseif ($throwable instanceof \UnexpectedValueException) {
            $status = self::ERROR_TEXT_CLIENT_EXCEPTION;
        }

        return new Badge((string) $throwable, $status, self::COLOR, $format, $style);
    }
}
