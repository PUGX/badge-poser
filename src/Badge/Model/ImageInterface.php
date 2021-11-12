<?php

declare(strict_types=1);

namespace App\Badge\Model;

interface ImageInterface
{
    public static function create(string $name, string $content, string $format = 'svg'): self;

    public function getOutputFileName(): string;
}
