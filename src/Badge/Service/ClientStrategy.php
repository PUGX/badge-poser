<?php

declare(strict_types=1);

namespace App\Badge\Service;

use App\Badge\Exception\RepositoryDataNotValid;
use App\Badge\Exception\SourceClientNotFound;
use App\Badge\ValueObject\Repository;
use Bitbucket\Client as BitbucketClient;
use Github\Api\Repo;
use Github\Client as GithubClient;
use Http\Client\Exception;

final class ClientStrategy implements ClientStrategyInterface
{
    private const GITHUB_REPOSITORY_PREFIX = 'blob';
    private const BITBUCKET_REPOSITORY_PREFIX = 'src';

    public function __construct(private GithubClient $githubClient, private BitbucketClient $bitbucketClient)
    {
    }

    /**
     * @throws SourceClientNotFound
     * @throws Exception
     */
    public function getDefaultBranch(Repository $repository): string
    {
        $defaultBranch = '';

        $username = $repository->getUsername();
        $repositoryName = $repository->getName();

        if (!$repository->isSupported()) {
            throw new SourceClientNotFound('Source Client '.$repository->getSource().' not found');
        }

        if ($repository->isGitHub()) {
            /** @var Repo $repoApi */
            $repoApi = $this->githubClient->api('repo');
            $repoGitHubData = $repoApi->show($username, $repositoryName);
            if (!$this->isValidGithubRepository($repoGitHubData)) {
                throw new RepositoryDataNotValid('Repository data not valid: '.\json_encode($repoGitHubData));
            }

            $defaultBranch = (string) $repoGitHubData['default_branch'];
        }

        if ($repository->isBitbucket()) {
            $repoBitbucketData = $this->bitbucketClient
                ->repositories()
                ->workspaces($username)
                ->show($repositoryName);

            if (!$this->isValidBitbucketRepository($repoBitbucketData)) {
                throw new RepositoryDataNotValid('Repository data not valid: '.\json_encode($repoBitbucketData));
            }

            $defaultBranch = (string) $repoBitbucketData['mainbranch']['name'];
        }

        return $defaultBranch;
    }

    public function getRepositoryPrefix(Repository $repository, string $repoUrl): string
    {
        $repositoryPrefixUrl = '';

        if (!$repository->isSupported()) {
            throw new SourceClientNotFound('Source Client '.$repository->getSource().' not found');
        }

        if ($repository->isGitHub()) {
            $repositoryPrefixUrl = $repoUrl.'/'.self::GITHUB_REPOSITORY_PREFIX;
        }

        if ($repository->isBitbucket()) {
            $repositoryPrefixUrl = \str_replace(
                'https://bitbucket.org',
                'https://api.bitbucket.org/2.0/repositories',
                $repoUrl
            );

            $repositoryPrefixUrl .= '/'.self::BITBUCKET_REPOSITORY_PREFIX;
        }

        return $repositoryPrefixUrl;
    }

    /**
     * @param array<mixed> $repoGitHubData
     */
    private function isValidGithubRepository(array $repoGitHubData): bool
    {
        return !empty($repoGitHubData)
            && \array_key_exists('default_branch', $repoGitHubData)
            && \is_string($repoGitHubData['default_branch']);
    }

    /**
     * @param array<mixed> $repoBitbucketData
     */
    private function isValidBitbucketRepository(array $repoBitbucketData): bool
    {
        return !empty($repoBitbucketData)
            && \array_key_exists('mainbranch', $repoBitbucketData)
            && \array_key_exists('name', $repoBitbucketData['mainbranch'])
            && \is_string($repoBitbucketData['mainbranch']['name']);
    }
}
