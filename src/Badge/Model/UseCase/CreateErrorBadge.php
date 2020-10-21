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
use App\Badge\Model\CacheableBadge;
use GuzzleHttp\Exception\BadResponseException;
use InvalidArgumentException;
use Throwable;
use UnexpectedValueException;

/**
 * Class CreateErrorBadge
 * Create the 'error' badge with the standard Font and standard Image.
 */
class CreateErrorBadge
{
    private const COLOR = 'e05d44';
    private const ERROR_TEXT_GENERIC = 'generic';
    private const ERROR_TEXT_CLIENT_EXCEPTION = 'connection';
    private const ERROR_TEXT_CLIENT_BAD_RESPONSE = 'not found?';

    /**
     * @throws InvalidArgumentException
     */
    public function createErrorBadge(Throwable $throwable, string $format): CacheableBadge
    {
        return new CacheableBadge(
            $this->createBadge($throwable, $format),
            CacheableBadge::TTL_NO_CACHE,
            CacheableBadge::TTL_NO_CACHE
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function createBadge(Throwable $throwable, string $format): Badge
    {
        $status = self::ERROR_TEXT_GENERIC;
        if ($throwable instanceof BadResponseException) {
            $status = self::ERROR_TEXT_CLIENT_BAD_RESPONSE;
        } elseif ($throwable instanceof UnexpectedValueException) {
            $status = self::ERROR_TEXT_CLIENT_EXCEPTION;
        }

        return new Badge((string) $throwable, $status, self::COLOR, $format);
    }
}
