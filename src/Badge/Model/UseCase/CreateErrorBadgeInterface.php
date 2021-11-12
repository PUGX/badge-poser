<?php

declare(strict_types=1);

namespace App\Badge\Model\UseCase;

use App\Badge\Model\CacheableBadge;
use Throwable;

interface CreateErrorBadgeInterface
{
    public function createErrorBadge(Throwable $throwable, string $format): CacheableBadge;
}
