<?php

declare(strict_types=1);

namespace App\Tests\Badge\Service;

use App\Badge\Exception\RepositoryDataNotValid;
use App\Badge\Exception\SourceClientNotFound;
use App\Badge\Service\ClientStrategy;
use App\Badge\ValueObject\Repository;
use Bitbucket\Api\Repositories;
use Bitbucket\Api\Repositories\Workspaces;
use Bitbucket\Client as BitbucketClient;
use Github\Api\Repo;
use Github\Client as GithubClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ClientStrategyTest extends TestCase
{
    /**
     * @var GithubClient|MockObject
     */
    private $githubClient;

    /**
     * @var BitbucketClient|MockObject
     */
    private $bitbucketClient;

    private ClientStrategy $clientStrategy;

    private string $username;

    private string $repositoryName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->githubClient = $this->getMockBuilder(GithubClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->bitbucketClient = $this->getMockBuilder(BitbucketClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->clientStrategy = new ClientStrategy($this->githubClient, $this->bitbucketClient);
        $this->username = 'username';
        $this->repositoryName = 'repositoryName';
    }

    public function testGetDefaultBranchFromGithub(): void
    {
        $defaultBranch = 'masterGithub';

        $apiInterface = $this->getMockBuilder(Repo::class)
            ->disableOriginalConstructor()
            ->getMock();
        $apiInterface->expects(self::once())
            ->method('show')
            ->with($this->username, $this->repositoryName)
            ->willReturn([
                'default_branch' => $defaultBranch,
            ]);

        $this->githubClient->expects(self::once())
            ->method('api')
            ->with('repo')
            ->willReturn($apiInterface);
        $source = 'github.com';
        self::assertEquals($defaultBranch, $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        ));
    }

    public function testGetDefaultBranchFromBitbucket(): void
    {
        $defaultBranch = 'masterBitbucket';

        $workspaces = $this->getMockBuilder(Workspaces::class)
            ->disableOriginalConstructor()
            ->getMock();
        $workspaces->expects(self::once())
            ->method('show')
            ->with($this->repositoryName)
            ->willReturn([
                'mainbranch' => [
                    'name' => $defaultBranch,
                ],
            ]);

        $repositories = $this->getMockBuilder(Repositories::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositories->expects(self::once())
            ->method('workspaces')
            ->with($this->username)
            ->willReturn($workspaces);

        $this->bitbucketClient->expects(self::once())
            ->method('repositories')
            ->willReturn($repositories);
        $source = 'bitbucket.org';
        self::assertEquals($defaultBranch, $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        ));
    }

    public function testThrowExceptionIfSourceClientIsNotFound(): void
    {
        $source = 'notManagedClient';

        $this->expectException(SourceClientNotFound::class);
        $this->expectExceptionMessage('Source Client notManagedClient not found');

        $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        );
    }

    public function testThrowExceptionIfEmptyGithubData(): void
    {
        $apiInterface = $this->getMockBuilder(Repo::class)
            ->disableOriginalConstructor()
            ->getMock();
        $apiInterface->expects(self::once())
            ->method('show')
            ->with($this->username, $this->repositoryName)->willReturn([]);

        $this->githubClient->expects(self::once())
            ->method('api')
            ->with('repo')
            ->willReturn($apiInterface);
        $source = 'github.com';

        $this->expectException(RepositoryDataNotValid::class);
        $this->expectExceptionMessage('Repository data not valid: []');

        $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        );
    }

    public function testThrowExceptionIfNotExistDefaultBranchKeyIntoGithubRepository(): void
    {
        $apiInterface = $this->getMockBuilder(Repo::class)
            ->disableOriginalConstructor()
            ->getMock();
        $apiInterface->expects(self::once())
            ->method('show')
            ->with($this->username, $this->repositoryName)
            ->willReturn([
                'foo' => 'bar',
            ]);

        $this->githubClient->expects(self::once())
            ->method('api')
            ->with('repo')
            ->willReturn($apiInterface);
        $source = 'github.com';

        $this->expectException(RepositoryDataNotValid::class);
        $this->expectExceptionMessage('Repository data not valid: {"foo":"bar"}');

        $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        );
    }

    public function testThrowExceptionIfDefaultBranchKeyIsNotStringIntoGithubRepository(): void
    {
        $apiInterface = $this->getMockBuilder(Repo::class)
            ->disableOriginalConstructor()
            ->getMock();
        $apiInterface->expects(self::once())
            ->method('show')
            ->with($this->username, $this->repositoryName)
            ->willReturn([
                'foo' => ['bar'],
            ]);

        $this->githubClient->expects(self::once())
            ->method('api')
            ->with('repo')
            ->willReturn($apiInterface);
        $source = 'github.com';

        $this->expectException(RepositoryDataNotValid::class);
        $this->expectExceptionMessage('Repository data not valid: {"foo":["bar"]}');

        $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        );
    }

    public function testThrowExceptionIfEmptyBitbucketData(): void
    {
        $workspaces = $this->getMockBuilder(Workspaces::class)
            ->disableOriginalConstructor()
            ->getMock();
        $workspaces->expects(self::once())
            ->method('show')
            ->with($this->repositoryName)
            ->willReturn([]);

        $repositories = $this->getMockBuilder(Repositories::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositories->expects(self::once())
            ->method('workspaces')
            ->with($this->username)
            ->willReturn($workspaces);

        $this->bitbucketClient->expects(self::once())
            ->method('repositories')
            ->willReturn($repositories);
        $source = 'bitbucket.org';

        $this->expectException(RepositoryDataNotValid::class);
        $this->expectExceptionMessage('Repository data not valid: []');

        $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        );
    }

    public function testThrowExceptionIfThereIsNoKeyMainBranchBitbucketData(): void
    {
        $workspaces = $this->getMockBuilder(Workspaces::class)
            ->disableOriginalConstructor()
            ->getMock();
        $workspaces->method('show')
            ->with($this->repositoryName)
            ->willReturn([
                'foo' => 'bar',
            ]);

        $repositories = $this->getMockBuilder(Repositories::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositories->expects(self::once())
            ->method('workspaces')
            ->with($this->username)
            ->willReturn($workspaces);

        $this->bitbucketClient->expects(self::once())
            ->method('repositories')
            ->willReturn($repositories);
        $source = 'bitbucket.org';

        $this->expectException(RepositoryDataNotValid::class);
        $this->expectExceptionMessage('Repository data not valid: {"foo":"bar"}');

        $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        );
    }

    public function testThrowExceptionIfThereIsNoKeyNameBitbucketData(): void
    {
        $workspaces = $this->getMockBuilder(Workspaces::class)
            ->disableOriginalConstructor()
            ->getMock();
        $workspaces->expects(self::once())
            ->method('show')
            ->with($this->repositoryName)
            ->willReturn([
                'mainbranch' => ['bar'],
            ]);

        $repositories = $this->getMockBuilder(Repositories::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositories->expects(self::once())
            ->method('workspaces')
            ->with($this->username)
            ->willReturn($workspaces);

        $this->bitbucketClient->expects(self::once())
            ->method('repositories')
            ->willReturn($repositories);
        $source = 'bitbucket.org';

        $this->expectException(RepositoryDataNotValid::class);
        $this->expectExceptionMessage('Repository data not valid: {"mainbranch":["bar"]}');

        $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        );
    }

    public function testThrowExceptionIfThereIsNNameIsNotStringBitbucketData(): void
    {
        $workspaces = $this->getMockBuilder(Workspaces::class)
            ->disableOriginalConstructor()
            ->getMock();
        $workspaces->expects(self::once())
            ->method('show')
            ->with($this->repositoryName)
            ->willReturn([
                'mainbranch' => [
                    'name' => ['bar'],
                ],
            ]);

        $repositories = $this->getMockBuilder(Repositories::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositories->expects(self::once())
            ->method('workspaces')
            ->with($this->username)
            ->willReturn($workspaces);

        $this->bitbucketClient->expects(self::once())
            ->method('repositories')
            ->willReturn($repositories);
        $source = 'bitbucket.org';

        $this->expectException(RepositoryDataNotValid::class);
        $this->expectExceptionMessage('Repository data not valid: {"mainbranch":{"name":["bar"]}}');

        $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        );
    }

    public function testShouldGetGithubComposerLink(): void
    {
        $source = 'github.com';
        $repoUrl = 'https://github.com/user/repo';

        $composerLockLinkNormalized = $this->clientStrategy->getRepositoryPrefix(
            Repository::create($source, $this->username, $this->repositoryName),
            $repoUrl
        );

        self::assertEquals($repoUrl.'/blob', $composerLockLinkNormalized);
    }

    public function testShouldGetBitbucketComposerLink(): void
    {
        $source = 'bitbucket.org';
        $repoUrl = 'https://bitbucket.org/user/repo';

        $composerLockLinkNormalized = $this->clientStrategy->getRepositoryPrefix(
            Repository::create($source, $this->username, $this->repositoryName),
            $repoUrl
        );

        self::assertEquals('https://api.bitbucket.org/2.0/repositories/user/repo/src', $composerLockLinkNormalized);
    }

    public function testShouldThrowExceptionIfSourceNotFoundForGetComposerLockLinkNormalized(): void
    {
        $source = 'notManagedClient';
        $repoUrl = 'https://notManaged.com/user/repo';

        $this->expectException(SourceClientNotFound::class);
        $this->expectExceptionMessage('Source Client notManagedClient not found');

        $this->clientStrategy->getRepositoryPrefix(
            Repository::create($source, $this->username, $this->repositoryName),
            $repoUrl
        );
    }
}
