<?php

namespace App\Stats\Reader;

/**
 * Interface ReaderInterface.
 */
interface ReaderInterface
{
    /**
     * Read total accesses.
     */
    public function totalAccess(): int;
}
