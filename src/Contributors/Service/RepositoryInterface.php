<?php

namespace App\Contributors\Service;

/**
 * Interface RepositoryInterface.
 */
interface RepositoryInterface
{
    /**
     * @return array<int|string, \App\Contributors\Model\Contributor>
     */
    public function all(): array;

    public function updateCache(): int;
}
