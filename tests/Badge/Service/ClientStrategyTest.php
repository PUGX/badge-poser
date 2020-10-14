<?php

declare(strict_types=1);

namespace App\Tests\Badge\Service;

use App\Badge\Exception\RepositoryDataNotValid;
use App\Badge\Exception\SourceClientNotFound;
use App\Badge\Service\ClientStrategy;
use App\Badge\ValueObject\Repository;
use Bitbucket\Api\Repositories;
use Bitbucket\Api\Repositories\Users;
use Bitbucket\Client as BitbucketClient;
use Github\Api\Repo;
use Github\Client as GithubClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientStrategyTest extends TestCase
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

    public function setUp(): void
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
        $apiInterface->expects($this->once())
            ->method('show')
            ->with($this->username, $this->repositoryName)
            ->willReturn([
                'default_branch' => $defaultBranch,
            ]);

        $this->githubClient->expects($this->once())
            ->method('api')
            ->with('repo')
            ->willReturn($apiInterface);
        $source = 'github.com';
        $this->assertEquals($defaultBranch, $this->clientStrategy->getDefaultBranch(
            Repository::create($source, $this->username, $this->repositoryName)
        ));
    }

    public function testGetDefaultBranchFromBitbucket(): void
    {
        $defaultBranch = 'masterBitbucket';

        $users = $this->getMockBuilder(Users::class)
            ->disableOriginalConstructor()
            ->getMock();
        $users->expects($this->once())
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
        $repositories->expects($this->once())
            ->method('users')
            ->with($this->username)
            ->willReturn($users);

        $this->bitbucketClient->expects($this->once())
            ->method('repositories')
            ->willReturn($repositories);
        $source = 'bitbucket.org';
        $this->assertEquals($defaultBranch, $this->clientStrategy->getDefaultBranch(
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
        $apiInterface->expects($this->once())
            ->method('show')
            ->with($this->username, $this->repositoryName)->willReturn([]);

        $this->githubClient->expects($this->once())
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
        $apiInterface->expects($this->once())
            ->method('show')
            ->with($this->username, $this->repositoryName)
            ->willReturn([
                'foo' => 'bar',
            ]);

        $this->githubClient->expects($this->once())
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
        $apiInterface->expects($this->once())
            ->method('show')
            ->with($this->username, $this->repositoryName)
            ->willReturn([
                'foo' => ['bar'],
            ]);

        $this->githubClient->expects($this->once())
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
        $users = $this->getMockBuilder(Users::class)
            ->disableOriginalConstructor()
            ->getMock();
        $users->expects($this->once())
            ->method('show')
            ->with($this->repositoryName)
            ->willReturn([]);

        $repositories = $this->getMockBuilder(Repositories::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositories->expects($this->once())
            ->method('users')
            ->with($this->username)
            ->willReturn($users);

        $this->bitbucketClient->expects($this->once())
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
        $users = $this->getMockBuilder(Users::class)
            ->disableOriginalConstructor()
            ->getMock();
        $users->method('show')
            ->with($this->repositoryName)
            ->willReturn([
                'foo' => 'bar',
            ]);

        $repositories = $this->getMockBuilder(Repositories::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositories->expects($this->once())
            ->method('users')
            ->with($this->username)
            ->willReturn($users);

        $this->bitbucketClient->expects($this->once())
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
        $users = $this->getMockBuilder(Users::class)
            ->disableOriginalConstructor()
            ->getMock();
        $users->expects($this->once())
            ->method('show')
            ->with($this->repositoryName)
            ->willReturn([
                'mainbranch' => ['bar'],
            ]);

        $repositories = $this->getMockBuilder(Repositories::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositories->expects($this->once())
            ->method('users')
            ->with($this->username)
            ->willReturn($users);

        $this->bitbucketClient->expects($this->once())
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
        $users = $this->getMockBuilder(Users::class)
            ->disableOriginalConstructor()
            ->getMock();
        $users->expects($this->once())
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
        $repositories->expects($this->once())
            ->method('users')
            ->with($this->username)
            ->willReturn($users);

        $this->bitbucketClient->expects($this->once())
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

        $this->assertEquals($repoUrl.'/blob', $composerLockLinkNormalized);
    }

    public function testShouldGetBitbucketComposerLink(): void
    {
        $source = 'bitbucket.org';
        $repoUrl = 'https://bitbucket.org/user/repo';

        $composerLockLinkNormalized = $this->clientStrategy->getRepositoryPrefix(
            Repository::create($source, $this->username, $this->repositoryName),
            $repoUrl
        );

        $this->assertEquals('https://api.bitbucket.org/2.0/repositories/user/repo/src', $composerLockLinkNormalized);
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
