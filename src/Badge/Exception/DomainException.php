<?php

declare(strict_types=1);

namespace App\Badge\Exception;

final class DomainException extends \Exception
{
    public static function sourceClientNotFound(string $sourceClient): self
    {
        return new static('Source Client '.$sourceClient.' not found');
    }

    public static function repositoryDataNotValid(string $data): self
    {
        return new static('Repository data not valid: '.$data);
    }
}
