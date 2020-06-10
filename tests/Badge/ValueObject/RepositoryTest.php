<?php

declare(strict_types=1);

namespace App\Tests\Badge\ValueObject;

use App\Badge\ValueObject\Repository;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class RepositoryTest extends TestCase
{
    public function testItShouldCreateFromRepositoryUrl(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://github.com/username/repository');

        $this->assertEquals('github.com', $repository->getSource());
        $this->assertEquals('username', $repository->getUsername());
        $this->assertEquals('repository', $repository->getName());
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

        $this->assertEquals('github.com', $repository->getSource());
        $this->assertEquals('username', $repository->getUsername());
        $this->assertEquals('repository', $repository->getName());
    }

    public function testItDetectGitHubAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://github.com/username/repository');

        $this->assertTrue($repository->isGitHub());
    }

    public function testGitHubShouldNotdetectedAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://fake-provider.com/username/repository');

        $this->assertFalse($repository->isGitHub());
    }

    public function testItSupportGitHubAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://github.com/username/repository');

        $this->assertTrue($repository->isSupported());
    }

    public function testItDetectBitbucketAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://bitbucket.org/username/repository');

        $this->assertTrue($repository->isBitbucket());
    }

    public function testBitbucketShouldNotdetectedAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://fake-provider.com/username/repository');

        $this->assertFalse($repository->isBitbucket());
    }

    public function testItSupportBitbucketAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://bitbucket.org/username/repository');

        $this->assertTrue($repository->isSupported());
    }

    /** @dataProvider unsupportedRepositrySourceProvider */
    public function testItDetectUnsupportedSourceProvider(string $sourceProviderUrl): void
    {
        $repository = Repository::createFromRepositoryUrl($sourceProviderUrl);

        $this->assertFalse($repository->isSupported());
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
