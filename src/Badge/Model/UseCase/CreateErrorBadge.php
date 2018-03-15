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
use Exception;
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
    private const SUBJECT = 'error';
    private const ERROR_TEXT_GENERIC = 'generic';
    private const ERROR_TEXT_CLIENT_EXCEPTION = 'connection';
    private const ERROR_TEXT_CLIENT_BAD_RESPONSE = 'not found?';

    /**
     * @param Exception $exception
     * @param string    $format
     *
     * @return Badge
     *
     * @throws InvalidArgumentException
     */
    public function createErrorBadge(Exception $exception, string $format): Badge
    {
        return $this->createBadge($exception, $format);
    }

    /**
     * @param Exception $exception
     * @param string    $format
     *
     * @return Badge
     *
     * @throws InvalidArgumentException
     */
    protected function createBadge(Exception $exception, string $format): Badge
    {
        $subject = 'error';
        $status = self::ERROR_TEXT_GENERIC;
        if ($exception instanceof BadResponseException) {
            $status = self::ERROR_TEXT_CLIENT_BAD_RESPONSE;
        } elseif ($exception instanceof UnexpectedValueException) {
            $status = self::ERROR_TEXT_CLIENT_EXCEPTION;
        }

        return new Badge((string) $exception, $status, self::COLOR, $format);
    }
}
