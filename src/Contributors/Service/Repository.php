<?php

namespace App\Contributors\Service;

use App\Contributors\Model\Contributor;
use Github\Client;
use Github\ResultPager;
use Predis\Client as Redis;

final class Repository implements RepositoryInterface
{
    private const REDIS_KEY_CONTRIBUTORS = 'CONTRIBUTORS';

    public function __construct(
        /* @var Redis<string, Redis> */
        private Redis $redis,
        private Client $client,
        private ResultPager $resultPager
    ) {
    }

    public function all(): array
    {
        try {
            $contributorsByCache = $this->getContributorsByCache();

            if (null !== $contributorsByCache) {
                return \unserialize($contributorsByCache, ['allowed_classes' => true]);
            }

            return $this->getContributors();
        } catch (\Exception) {
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
    private function getContributors(): array
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

        $this->setContributorsInCache($contributors);

        return $contributors;
    }

    /**
     * @return array<int, array<string>>
     */
    private function getContributorsByGithub(string $username, string $repoName): array
    {
        $repoApi = $this->client->api('repo');
        $parameters = [$username, $repoName];

        return $this->resultPager->fetchAll(
            $repoApi,
            'contributors',
            $parameters
        );
    }
}
