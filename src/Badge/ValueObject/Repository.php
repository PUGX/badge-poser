<?php

declare(strict_types=1);

namespace App\Badge\ValueObject;

final class Repository
{
    private const GITHUB_SOURCE = 'github.com';

    private const BITBUCKET_SOURCE = 'bitbucket.org';

    private const GITLAB_SOURCE = 'gitlab.com';

    private function __construct(
        private readonly string $source,
        private readonly string $username,
        private readonly string $name,
    ) {
    }

    public static function create(string $source, string $username, string $name): self
    {
        return new self($source, $username, $name);
    }

    public static function createFromRepositoryUrl(string $repositoryUrl): self
    {
        \preg_match('/(https)(:\/\/|@)([^\/:]+)[\/:]([^\/:]+)\/(.+)$/', $repositoryUrl, $matches);

        if (!isset($matches[3], $matches[4], $matches[5])) {
            throw new \UnexpectedValueException(\sprintf('Impossible to fetch package by "%s" repository.', $repositoryUrl));
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
        return self::GITHUB_SOURCE === $this->getSource();
    }

    public function isBitbucket(): bool
    {
        return self::BITBUCKET_SOURCE === $this->getSource();
    }

    public function isGitLab(): bool
    {
        return self::GITLAB_SOURCE === $this->getSource();
    }

    public function isSupported(): bool
    {
        return $this->isGitHub() || $this->isBitbucket() || $this->isGitLab();
    }
}
