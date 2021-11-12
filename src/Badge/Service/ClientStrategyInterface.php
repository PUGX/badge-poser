<?php

declare(strict_types=1);

namespace App\Badge\Service;

use App\Badge\ValueObject\Repository;

interface ClientStrategyInterface
{
    public function getDefaultBranch(Repository $repository): string;

    public function getRepositoryPrefix(Repository $repository, string $repoUrl): string;
}
