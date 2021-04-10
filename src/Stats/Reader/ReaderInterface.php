<?php

namespace App\Stats\Reader;

interface ReaderInterface
{
    /**
     * Read total accesses.
     */
    public function totalAccess(): int;
}
