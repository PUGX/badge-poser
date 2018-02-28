<?php


namespace App\Contributors\Service;


interface RepositoryInterface
{
    /**
     * @return array
     */
    public function all(): array;

    /**
     * @return int
     */
    public function updateCache(): int;
}