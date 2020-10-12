<?php

namespace App\DateProvider;

interface DateTimeProviderInterface
{
    public function getDateTime(): \DateTimeInterface;

    public function getTime(): int;
}
