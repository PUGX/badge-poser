<?php

declare(strict_types=1);

namespace App\Badge\ValueObject;

use UnexpectedValueException;

class Repository
{
    private const GITHUB_SOURCE = 'github.com';

    private const BITBUCKET_SOURCE = 'bitbucket.org';

    private string $source;

    private string $username;

    private string $name;

    private function __construct(string $source, string $username, string $name)
    {
        $this->source = $source;
        $this->username = $username;
        $this->name = $name;
    }

    public static function create(string $source, string $username, string $name): self
    {
        return new self($source, $username, $name);
    }

    public static function createFromRepositoryUrl(string $repositoryUrl): self
    {
        \preg_match('/(https)(:\/\/|@)([^\/:]+)[\/:]([^\/:]+)\/(.+)$/', $repositoryUrl, $matches);

        if (!isset($matches[3], $matches[4], $matches[5])) {
            throw new UnexpectedValueException(\sprintf('Impossible to fetch package by "%s" repository.', $repositoryUrl));
        }

        return new self($matches[3], $matches[4], $matches[5]);
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isGitHub(): bool
    {
        if (self::GITHUB_SOURCE === $this->getSource()) {
            return true;
        }

        return false;
    }

    public function isBitbucket(): bool
    {
        if (self::BITBUCKET_SOURCE === $this->getSource()) {
            return true;
        }

        return false;
    }

    public function isSupported(): bool
    {
        if ($this->isGitHub() || $this->isBitbucket()) {
            return true;
        }

        return false;
    }
}
