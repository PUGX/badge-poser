<?php

namespace App\DateProvider;

/**
 * @codeCoverageIgnore
 */
final class CurrentDateTimeProvider implements DateTimeProviderInterface
{
    public function getDateTime(): \DateTimeInterface
    {
        return new \DateTime();
    }

    public function getTime(): int
    {
        return $this->getDateTime()->getTimestamp();
    }
}
