<?php

namespace App\Service;

interface GitLabClientInterface
{
    /** @return array<string, mixed> */
    public function health(): array;

    /** @return array<string, mixed> */
    public function show(string $project): array;
}
