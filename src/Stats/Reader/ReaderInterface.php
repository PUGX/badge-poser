<?php

namespace App\Stats\Reader;

interface ReaderInterface
{
    /**
     * Read total accesses.
     *
     * @return int
     */
    public function totalAccess(): int;
}
