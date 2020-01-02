<?php

namespace App\Contributors\Service;

/**
 * Interface RepositoryInterface.
 */
interface RepositoryInterface
{
    public function all(): array;

    public function updateCache(): int;
}
