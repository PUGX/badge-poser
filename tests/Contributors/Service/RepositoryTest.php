<?php

namespace App\Tests\Contributors\Service;

use App\Contributors\Model\Contributor;
use App\Contributors\Service\Repository;
use Github\Api\AbstractApi;
use Github\Client;
use Github\ResultPager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Predis\Client as Redis;

final class RepositoryTest extends TestCase
{
    /** @var ResultPager|MockObject */
    private MockObject $resultPager;

    private Repository $repository;

    protected function setUp(): void
    {
        $redis = $this->createMock(Redis::class);

        $client = $this->createMock(Client::class);

        $api = $this->createMock(AbstractApi::class);

        $client
            ->method('api')
            ->willReturn($api);

        $this->resultPager = $this->createMock(ResultPager::class);

        $this->repository = new Repository($redis, $client, $this->resultPager);
    }

    public function testAll(): void
    {
        $fetchAllValueExpect = $this->getFakeResultPagerFetchAll();

        $this->resultPager
            ->method('fetchAll')
            ->willReturn($fetchAllValueExpect);

        $contributors = $this->repository->all();

        self::assertNotEmpty($contributors);

        foreach ($contributors as $k => $contributor) {
            self::assertInstanceOf(Contributor::class, $contributor);

            if ('liuggio' === $k) {
                self::assertEquals('liuggio', $contributor->getUsername());
            } elseif ('leopro' === $k) {
                self::assertEquals('leopro', $contributor->getUsername());
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

        self::assertEquals(\count($fetchAllValueExpect), $count);
    }

    private function checkUrl(string $url): void
    {
        $data = \file_get_contents($url);
        self::assertNotFalse((bool) $data, 'Unable to open URL: '.$url);
        self::assertGreaterThan(0, \strlen((string) $data));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getFakeResultPagerFetchAll(): array
    {
        return [
            [
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
            [
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
