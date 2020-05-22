<?php

namespace App\Contributors\Service;

use App\Contributors\Model\Contributor;
use Github\Client;
use Github\ResultPager;
use Predis\Client as Redis;

/**
 * Class Repository.
 */
class Repository implements RepositoryInterface
{
    private const REDIS_KEY_CONTRIBUTORS = 'CONTRIBUTORS';

    /** @var Redis<string, Redis> */
    protected Redis $redis;
    protected Client $client;
    protected ResultPager $resultPager;

    /** @param Redis<string, Redis> $redis */
    public function __construct(Redis $redis, Client $client, ResultPager $resultPager)
    {
        $this->redis = $redis;
        $this->client = $client;
        $this->resultPager = $resultPager;
    }

    public function all(): array
    {
        try {
            $contributorsByCache = $this->getContributorsByCache();

            if (null !== $contributorsByCache) {
                return \unserialize($contributorsByCache, ['allowed_classes' => true]);
            }

            return $this->getContributors();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function updateCache(): int
    {
        $contributors = $this->getContributors();

        return \count($contributors);
    }

    private function getContributorsByCache(): ?string
    {
        return $this->redis->get(self::REDIS_KEY_CONTRIBUTORS);
    }

    /**
     * @param array<Contributor> $contributors
     */
    private function setContributorsInCache(array $contributors): void
    {
        $this->redis->set(self::REDIS_KEY_CONTRIBUTORS, \serialize($contributors));
    }

    /**
     * @return array<Contributor>
     */
    private function getContributors(bool $setCache = true): array
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
     * @return array<int, array<string>>
     */
    private function getContributorsByGithub(string $username, string $repoName): array
    {
        $repoApi = $this->client->api('repo');
        $parameters = [$username, $repoName];
        $results = $this->resultPager->fetchAll(
            $repoApi,
            'contributors',
            $parameters
        );

        return $results;
    }
}
