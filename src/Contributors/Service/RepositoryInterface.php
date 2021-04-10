<?php

namespace App\Contributors\Service;

interface RepositoryInterface
{
    /**
     * @return array<int|string, \App\Contributors\Model\Contributor>
     */
    public function all(): array;

    public function updateCache(): int;
}
