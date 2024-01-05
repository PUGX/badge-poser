<?php

namespace App\Tests\Service;

use App\Badge\Exception\RepositoryDataNotValid;
use App\Service\GitLabClient;
use Gitlab\Api\Projects;
use Gitlab\Client;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class GitLabClientTest extends TestCase
{
    use ProphecyTrait;

    public function testGetDataFromClient(): void
    {
        $apiProjects = $this->prophesize(Projects::class);
        $apiProjects->show('repositoryName')->willReturn(['data'])->shouldBeCalled();
        $client = $this->prophesize(Client::class);
        $client->authenticate('token', Client::AUTH_HTTP_TOKEN);
        $client->projects()->willReturn($apiProjects);
        $gitlabClient = new GitLabClient($client->reveal(), 'token');
        $gitlabClient->show('repositoryName');
    }

    public function testThrowErrorsIfDataIsNotAnArrayDataFromClient(): void
    {
        $this->expectException(RepositoryDataNotValid::class);

        $apiProjects = $this->prophesize(Projects::class);
        $apiProjects->show('repositoryName')->willReturn('stringValue')->shouldBeCalled();
        $client = $this->prophesize(Client::class);
        $client->authenticate('token', Client::AUTH_HTTP_TOKEN);
        $client->projects()->willReturn($apiProjects);
        $gitlabClient = new GitLabClient($client->reveal(), 'token');
        $gitlabClient->show('repositoryName');
    }
}
