<?php

namespace App\Stats\Reader;

/**
 * Class NullReader.
 */
final class NullReader implements ReaderInterface
{
    public static $totalAccess = 11111;

    public function totalAccess(): int
    {
        return static::$totalAccess;
    }
}
