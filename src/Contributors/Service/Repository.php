<?php

namespace App\Contributors\Service;

use App\Contributors\Model\Contributor;
use Github\Client;
use Github\ResultPager;
use Predis\Client as Redis;

class Repository implements RepositoryInterface
{
    private const REDIS_KEY_CONTRIBUTORS = 'CONTRIBUTORS';

    protected $redis;
    protected $client;
    protected $resultPager;

    public function __construct(Redis $redis, Client $client, ResultPager $resultPager)
    {
        $this->redis = $redis;
        $this->client = $client;
        $this->resultPager = $resultPager;
    }

    /**
     * @return Contributor[]
     */
    public function all(): array
    {
        try {
            $contributorsByCache = $this->getContributorsByCache();

            if (null !== $contributorsByCache) {
                return unserialize($contributorsByCache, ['allowed_classes' => true]);
            }

            return $this->getContributors();

        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @return int
     */
    public function updateCache(): int
    {
        $contributors = $this->getContributors();

        return \count($contributors);
    }

    /**
     * @return null|string
     */
    private function getContributorsByCache(): ?string
    {
        return $this->redis->get(self::REDIS_KEY_CONTRIBUTORS);
    }

    /**
     * @param Contributor[] $contributors
     */
    private function setContributorsInCache($contributors): void
    {
        $this->redis->set(self::REDIS_KEY_CONTRIBUTORS, serialize($contributors));
    }

    /**
     * @param bool $setCache
     * @return Contributor[]
     */
    private function getContributors($setCache = true): array
    {
        $contributors = [];

        $results = $this->getContributorsByGithub('PUGX', 'badge-poser');
        foreach ($results as $result) {
            $contributors[$result['login']] = Contributor::create($result['login'], $result['html_url'], $result['avatar_url']);
        }
        $results = $this->getContributorsByGithub('badges', 'poser');
        foreach ($results as $result) {
            $contributors[$result['login']] = Contributor::create($result['login'], $result['html_url'], $result['avatar_url']);
        }

        if ($setCache) {
            $this->setContributorsInCache($contributors);
        }

        return $contributors;
    }

    /**
     * @param $username
     * @param $repoName
     * @return array
     */
    private function getContributorsByGithub($username, $repoName): array
    {
        try {
            $repoApi = $this->client->api('repo');
            $parameters = [$username, $repoName];
            $results = $this->resultPager->fetchAll(
                $repoApi,
                'contributors',
                $parameters
            );
        } catch (\Exception $e) {
            return [];
        }

        return $results;
    }
}
