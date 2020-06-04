<?php

declare(strict_types=1);

namespace App\Badge\Service;

use App\Badge\Exception\DomainException;
use Bitbucket\Client as BitbucketClient;
use Github\Api\Repo;
use Github\Client as GithubClient;

class ClientStrategy
{
    private const GITHUB_SOURCE = 'github.com';
    private const BITBUCKET_SOURCE = 'bitbucket.org';

    private GithubClient $githubClient;

    private BitbucketClient $bitbucketClient;

    public function __construct(GithubClient $githubClient, BitbucketClient $bitbucketClient)
    {
        $this->githubClient = $githubClient;
        $this->bitbucketClient = $bitbucketClient;
    }

    public function getDefaultBranch(string $source, string $username, string $repositoryName): string
    {
        $defaultBranch = '';

        if (self::GITHUB_SOURCE !== $source && self::BITBUCKET_SOURCE !== $source) {
            throw DomainException::sourceClientNotFound($source);
        }

        if (self::GITHUB_SOURCE === $source) {
            /** @var Repo $repoApi */
            $repoApi = $this->githubClient->api('repo');
            $repoGitHubData = $repoApi->show($username, $repositoryName);
            if (!$this->isValidGithubRepository($repoGitHubData)) {
                throw DomainException::repositoryDataNotValid((string) \json_encode($repoGitHubData));
            }

            $defaultBranch = $repoGitHubData['default_branch'];
        }

        if (self::BITBUCKET_SOURCE === $source) {
            $repoBitbucketData = $this->bitbucketClient
                ->repositories()
                ->users($username)
                ->show($repositoryName);

            if (!$this->isValidBitbucketRepository($repoBitbucketData)) {
                throw DomainException::repositoryDataNotValid((string) \json_encode($repoBitbucketData));
            }

            $defaultBranch = $repoBitbucketData['mainbranch']['name'];
        }

        return $defaultBranch;
    }

    /**
     * @param mixed[] $repoGitHubData
     */
    private function isValidGithubRepository(array $repoGitHubData): bool
    {
        return !empty($repoGitHubData)
            && \array_key_exists('default_branch', $repoGitHubData)
            && \is_string($repoGitHubData['default_branch']);
    }

    /**
     * @param mixed[] $repoBitbucketData
     */
    private function isValidBitbucketRepository(array $repoBitbucketData): bool
    {
        return !empty($repoBitbucketData)
            && \array_key_exists('mainbranch', $repoBitbucketData)
            && \array_key_exists('name', $repoBitbucketData['mainbranch'])
            && \is_string($repoBitbucketData['mainbranch']['name']);
    }
}
