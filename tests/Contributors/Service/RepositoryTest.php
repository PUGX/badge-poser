<?php

namespace App\Tests\Contributors\Service;

use App\Contributors\Model\Contributor;
use App\Contributors\Service\Repository;
use Github\Api\ApiInterface;
use Github\Client;
use Github\ResultPager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Predis\Client as Redis;

/**
 * Class RepositoryTest.
 */
final class RepositoryTest extends TestCase
{
    private const API_CAN_VISIT = 'https://helloacm.com/api/can-visit/';
    /** @var ResultPager|MockObject */
    private $resultPager;

    private Repository $repository;

    protected function setUp(): void
    {
        $redis = $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()->getMock();

        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()->getMock();

        $api = $this->getMockBuilder(ApiInterface::class)
            ->disableOriginalConstructor()->getMock();

        $client
            ->method('api')
            ->willReturn($api);

        $this->resultPager = $this->getMockBuilder(ResultPager::class)
            ->disableOriginalConstructor()->getMock();

        $this->repository = new Repository($redis, $client, $this->resultPager);
    }

    public function testAll(): void
    {
        $fetchAllValueExpect = $this->getFakeResultPagerFetchAll();

        $this->resultPager
            ->method('fetchAll')
            ->willReturn($fetchAllValueExpect);

        $contributors = $this->repository->all();

        $this->assertNotEmpty($contributors);

        foreach ($contributors as $k => $contributor) {
            $this->assertInstanceOf(Contributor::class, $contributor);

            if ('liuggio' === $k) {
                $this->assertEquals('liuggio', $contributor->getUsername());
            } elseif ('leopro' === $k) {
                $this->assertEquals('leopro', $contributor->getUsername());
            }

            $this->checkUrl($contributor->getProfileUrl());
            $this->checkUrl($contributor->getProfileImg());
        }
    }

    public function testUpdateCache(): void
    {
        $fetchAllValueExpect = $this->getFakeResultPagerFetchAll();

        $this->resultPager
            ->method('fetchAll')
            ->willReturn($fetchAllValueExpect);

        $count = $this->repository->updateCache();

        $this->assertEquals(\count($fetchAllValueExpect), $count);
    }

    private function checkUrl($url): void
    {
        $data = \file_get_contents(self::API_CAN_VISIT.'?url='.$url);
        $result = \json_decode($data, true);
        $this->assertTrue($result['result']);
        $this->assertEquals(200, $result['code']);
    }

    private function getFakeResultPagerFetchAll(): array
    {
        return [
            0 => [
                'login' => 'liuggio',
                'id' => 530406,
                'avatar_url' => 'https://avatars1.githubusercontent.com/u/530406?v=4',
                'gravatar_id' => '',
                'url' => 'https://api.github.com/users/liuggio',
                'html_url' => 'https://github.com/liuggio',
                'followers_url' => 'https://api.github.com/users/liuggio/followers',
                'following_url' => 'https://api.github.com/users/liuggio/following{/other_user}',
                'gists_url' => 'https://api.github.com/users/liuggio/gists{/gist_id}',
                'starred_url' => 'https://api.github.com/users/liuggio/starred{/owner}{/repo}',
                'subscriptions_url' => 'https://api.github.com/users/liuggio/subscriptions',
                'organizations_url' => 'https://api.github.com/users/liuggio/orgs',
                'repos_url' => 'https://api.github.com/users/liuggio/repos',
                'events_url' => 'https://api.github.com/users/liuggio/events{/privacy}',
                'received_events_url' => 'https://api.github.com/users/liuggio/received_events',
                'type' => 'User',
                'site_admin' => false,
                'contributions' => 186,
            ],
            1 => [
                'login' => 'leopro',
                'id' => 1370900,
                'avatar_url' => 'https://avatars2.githubusercontent.com/u/1370900?v=4',
                'gravatar_id' => '',
                'url' => 'https://api.github.com/users/leopro',
                'html_url' => 'https://github.com/leopro',
                'followers_url' => 'https://api.github.com/users/leopro/followers',
                'following_url' => 'https://api.github.com/users/leopro/following{/other_user}',
                'gists_url' => 'https://api.github.com/users/leopro/gists{/gist_id}',
                'starred_url' => 'https://api.github.com/users/leopro/starred{/owner}{/repo}',
                'subscriptions_url' => 'https://api.github.com/users/leopro/subscriptions',
                'organizations_url' => 'https://api.github.com/users/leopro/orgs',
                'repos_url' => 'https://api.github.com/users/leopro/repos',
                'events_url' => 'https://api.github.com/users/leopro/events{/privacy}',
                'received_events_url' => 'https://api.github.com/users/leopro/received_events',
                'type' => 'User',
                'site_admin' => false,
                'contributions' => 29,
            ],
        ];
    }
}
