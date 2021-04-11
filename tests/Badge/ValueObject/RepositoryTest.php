<?php

declare(strict_types=1);

namespace App\Tests\Badge\ValueObject;

use App\Badge\ValueObject\Repository;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class RepositoryTest extends TestCase
{
    public function testItShouldCreateFromRepositoryUrl(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://github.com/username/repository');

        self::assertEquals('github.com', $repository->getSource());
        self::assertEquals('username', $repository->getUsername());
        self::assertEquals('repository', $repository->getName());
    }

    public function testItThrowExceptionIfUrlNotValid(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Impossible to fetch package by "https://google.it" repository.');

        Repository::createFromRepositoryUrl('https://google.it');
    }

    public function testItShouldCreateRepository(): void
    {
        $repository = Repository::create('github.com', 'username', 'repository');

        self::assertEquals('github.com', $repository->getSource());
        self::assertEquals('username', $repository->getUsername());
        self::assertEquals('repository', $repository->getName());
    }

    public function testItDetectGitHubAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://github.com/username/repository');

        self::assertTrue($repository->isGitHub());
    }

    public function testGitHubShouldNotdetectedAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://fake-provider.com/username/repository');

        self::assertFalse($repository->isGitHub());
    }

    public function testItSupportGitHubAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://github.com/username/repository');

        self::assertTrue($repository->isSupported());
    }

    public function testItDetectBitbucketAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://bitbucket.org/username/repository');

        self::assertTrue($repository->isBitbucket());
    }

    public function testBitbucketShouldNotdetectedAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://fake-provider.com/username/repository');

        self::assertFalse($repository->isBitbucket());
    }

    public function testItSupportBitbucketAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://bitbucket.org/username/repository');

        self::assertTrue($repository->isSupported());
    }

    /** @dataProvider unsupportedRepositrySourceProvider */
    public function testItDetectUnsupportedSourceProvider(string $sourceProviderUrl): void
    {
        $repository = Repository::createFromRepositoryUrl($sourceProviderUrl);

        self::assertFalse($repository->isSupported());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function unsupportedRepositrySourceProvider(): \Generator
    {
        yield ['https://www.gitlab.com/username/repository'];
        yield ['https://www.my-self-hosted-git.com/acme/foo'];
        yield ['https://www.fake-provider.com/foo/bar'];
    }
}
