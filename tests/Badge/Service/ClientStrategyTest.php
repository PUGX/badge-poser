<?php

declare(strict_types=1);

namespace App\Tests\Badge\Service;

use App\Badge\Exception\DomainException;
use App\Badge\Service\ClientStrategy;
use Bitbucket\Api\Repositories;
use Bitbucket\Api\Repositories\Users;
use Bitbucket\Client as BitbucketClient;
use Github\Api\Repo;
use Github\Client as GithubClient;
use PHPUnit\Framework\TestCase;

class ClientStrategyTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|GithubClient
     */
    private $githubClient;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|BitbucketClient
     */
    private $bitbucketClient;

    private ClientStrategy $clientStrategy;

    private string $username;

    private string $repositoryName;

    public function setUp(): void
    {
        parent::setUp();
        $this->githubClient = $this->prophesize()->willExtend(GithubClient::class);
        $this->bitbucketClient = $this->prophesize()->willExtend(BitbucketClient::class);
        $this->clientStrategy = new ClientStrategy($this->githubClient->reveal(), $this->bitbucketClient->reveal());
        $this->username = 'username';
        $this->repositoryName = 'repositoryName';
    }

    public function testGetDefaultBranchFromGithub(): void
    {
        $defaultBranch = 'masterGithub';

        $apiInterface = $this->prophesize(Repo::class);
        $apiInterface->show($this->username, $this->repositoryName)->willReturn(
            [
                'default_branch' => $defaultBranch,
            ]
        )->shouldBeCalledTimes(1);

        $this->githubClient->api('repo')->willReturn($apiInterface)->shouldBeCalledTimes(1);
        $source = 'github.com';
        $this->assertEquals($defaultBranch, $this->clientStrategy->getDefaultBranch(
            $source,
            $this->username,
            $this->repositoryName
        ));
    }

    public function testGetDefaultBranchFromBitbucket(): void
    {
        $defaultBranch = 'masterBitbucket';

        $users = $this->prophesize(Users::class);
        $users->show($this->repositoryName)->willReturn(
            [
                'mainbranch' => [
                    'name' => $defaultBranch,
                ],
            ]
        )->shouldBeCalledTimes(1);

        $repositories = $this->prophesize(Repositories::class);
        $repositories->users($this->username)->willReturn($users)->shouldBeCalledTimes(1);

        $this->bitbucketClient->repositories()->willReturn($repositories)->shouldBeCalledTimes(1);
        $source = 'bitbucket.org';
        $this->assertEquals($defaultBranch, $this->clientStrategy->getDefaultBranch(
            $source,
            $this->username,
            $this->repositoryName
        ));
    }

    public function testThrowExceptionIfSourceClientIsNotFound(): void
    {
        $source = 'notManagedClient';

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Source Client notManagedClient not found');

        $this->clientStrategy->getDefaultBranch(
            $source,
            $this->username,
            $this->repositoryName
        );
    }

    public function testThrowExceptionIfEmptyGithubData(): void
    {
        $apiInterface = $this->prophesize(Repo::class);
        $apiInterface->show($this->username, $this->repositoryName)->willReturn([])->shouldBeCalledTimes(1);

        $this->githubClient->api('repo')->willReturn($apiInterface)->shouldBeCalledTimes(1);
        $source = 'github.com';

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Repository data not valid: []');

        $this->clientStrategy->getDefaultBranch(
            $source,
            $this->username,
            $this->repositoryName
        );
    }

    public function testThrowExceptionIfNotExistDefaultBranchKeyIntoGithubRepository(): void
    {
        $apiInterface = $this->prophesize(Repo::class);
        $apiInterface->show($this->username, $this->repositoryName)->willReturn([
            'foo' => 'bar',
        ])->shouldBeCalledTimes(1);

        $this->githubClient->api('repo')->willReturn($apiInterface)->shouldBeCalledTimes(1);
        $source = 'github.com';

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Repository data not valid: {"foo":"bar"}');

        $this->clientStrategy->getDefaultBranch(
            $source,
            $this->username,
            $this->repositoryName
        );
    }

    public function testThrowExceptionIfDefaultBranchKeyIsNotStringIntoGithubRepository(): void
    {
        $apiInterface = $this->prophesize(Repo::class);
        $apiInterface->show($this->username, $this->repositoryName)->willReturn([
            'foo' => ['bar'],
        ])->shouldBeCalledTimes(1);

        $this->githubClient->api('repo')->willReturn($apiInterface)->shouldBeCalledTimes(1);
        $source = 'github.com';

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Repository data not valid: {"foo":["bar"]}');

        $this->clientStrategy->getDefaultBranch(
            $source,
            $this->username,
            $this->repositoryName
        );
    }

    public function testThrowExceptionIfEmptyBitbucketData(): void
    {
        $users = $this->prophesize(Users::class);
        $users->show($this->repositoryName)->willReturn(
            []
        )->shouldBeCalledTimes(1);

        $repositories = $this->prophesize(Repositories::class);
        $repositories->users($this->username)->willReturn($users)->shouldBeCalledTimes(1);

        $this->bitbucketClient->repositories()->willReturn($repositories)->shouldBeCalledTimes(1);
        $source = 'bitbucket.org';

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Repository data not valid: []');

        $this->clientStrategy->getDefaultBranch(
            $source,
            $this->username,
            $this->repositoryName
        );
    }

    public function testThrowExceptionIfThereIsNoKeyMainBranchBitbucketData(): void
    {
        $users = $this->prophesize(Users::class);
        $users->show($this->repositoryName)->willReturn(
            ['foo' => 'bar']
        )->shouldBeCalledTimes(1);

        $repositories = $this->prophesize(Repositories::class);
        $repositories->users($this->username)->willReturn($users)->shouldBeCalledTimes(1);

        $this->bitbucketClient->repositories()->willReturn($repositories)->shouldBeCalledTimes(1);
        $source = 'bitbucket.org';

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Repository data not valid: {"foo":"bar"}');

        $this->clientStrategy->getDefaultBranch(
            $source,
            $this->username,
            $this->repositoryName
        );
    }

    public function testThrowExceptionIfThereIsNoKeyNameBitbucketData(): void
    {
        $users = $this->prophesize(Users::class);
        $users->show($this->repositoryName)->willReturn(
            ['mainbranch' => ['bar']]
        )->shouldBeCalledTimes(1);

        $repositories = $this->prophesize(Repositories::class);
        $repositories->users($this->username)->willReturn($users)->shouldBeCalledTimes(1);

        $this->bitbucketClient->repositories()->willReturn($repositories)->shouldBeCalledTimes(1);
        $source = 'bitbucket.org';

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Repository data not valid: {"mainbranch":["bar"]}');

        $this->clientStrategy->getDefaultBranch(
            $source,
            $this->username,
            $this->repositoryName
        );
    }

    public function testThrowExceptionIfThereIsNNameIsNotStringBitbucketData(): void
    {
        $users = $this->prophesize(Users::class);
        $users->show($this->repositoryName)->willReturn(
            [
                'mainbranch' => [
                    'name' => ['bar'],
                ],
            ]
        )->shouldBeCalledTimes(1);

        $repositories = $this->prophesize(Repositories::class);
        $repositories->users($this->username)->willReturn($users)->shouldBeCalledTimes(1);

        $this->bitbucketClient->repositories()->willReturn($repositories)->shouldBeCalledTimes(1);
        $source = 'bitbucket.org';

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Repository data not valid: {"mainbranch":{"name":["bar"]}}');

        $this->clientStrategy->getDefaultBranch(
            $source,
            $this->username,
            $this->repositoryName
        );
    }
}
