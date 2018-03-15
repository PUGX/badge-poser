<?php

namespace App\Stats\Reader;

/**
 * Interface ReaderInterface.
 */
interface ReaderInterface
{
    /**
     * Read total accesses.
     *
     * @return int
     */
    public function totalAccess(): int;
}
