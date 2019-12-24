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
use Throwable;
use GuzzleHttp\Exception\BadResponseException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class CreateErrorBadge
 * Create the 'error' badge with the standard Font and standard Image.
 */
class CreateErrorBadge
{
    private const COLOR = 'e05d44';
    private const SUBJECT = 'error';    // TODO this private constant is unused
    private const ERROR_TEXT_GENERIC = 'generic';
    private const ERROR_TEXT_CLIENT_EXCEPTION = 'connection';
    private const ERROR_TEXT_CLIENT_BAD_RESPONSE = 'not found?';

    /**
     * @throws InvalidArgumentException
     */
    public function createErrorBadge(Throwable $throwable, string $format): Badge
    {
        return $this->createBadge($throwable, $format);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function createBadge(Throwable $throwable, string $format): Badge
    {
        $subject = 'error';
        $status = self::ERROR_TEXT_GENERIC;
        if ($throwable instanceof BadResponseException) {
            $status = self::ERROR_TEXT_CLIENT_BAD_RESPONSE;
        } elseif ($throwable instanceof UnexpectedValueException) {
            $status = self::ERROR_TEXT_CLIENT_EXCEPTION;
        }

        return new Badge((string) $throwable, $status, self::COLOR, $format);
    }
}
