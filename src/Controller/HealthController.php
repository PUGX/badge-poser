<?php

namespace App\Controller;

use App\Service\CircleCiClientInterface;
use App\Service\GitLabClientInterface;
use Bitbucket\Client as BitbucketClient;
use Packagist\Api\Client as PackagistClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HealthController extends AbstractController
{
    // sample repo taken from https://github.com/PUGX/badge-poser/issues/337
    private const string BITBUCKET_HEALTHCHECK_WORKSPACE = 'wirbelwild';
    private const string BITBUCKET_HEALTHCHECK_REPO = 'kiwa-core';

    #[Route('/health', name: 'health')]
    public function health(
        PackagistClient $packagistClient,
        CircleCiClientInterface $circleCiClient,
        GitLabClientInterface $gitlabClient,
        BitbucketClient $bitbucketClient,
    ): Response {
        $packagistPopular = $packagistClient->popular(1);
        $packagistSuccessRequest = \count($packagistPopular) > 0 && !empty($packagistPopular[0]->getName()) && $packagistPopular[0]->getDownloads() > 0;

        $circleciHealth = \json_decode($circleCiClient->health()->getContent());
        $circleciSuccessRequest = !empty($circleciHealth) && !empty($circleciHealth->id);

        $gitlabHealth = $gitlabClient->health();
        $gitlabSuccessRequest = !empty($gitlabHealth) && !empty($gitlabHealth['id']);

        $bitbucketHealth = $bitbucketClient->repositories()->workspaces(self::BITBUCKET_HEALTHCHECK_WORKSPACE)->show(self::BITBUCKET_HEALTHCHECK_REPO);
        $bitbucketSuccessRequest = !empty($bitbucketHealth) && !empty($bitbucketHealth['full_name']);

        $response = $this->render(
            'health/index.txt.twig',
            [
                'packagist' => $packagistSuccessRequest,
                'circleci' => $circleciSuccessRequest,
                'gitlab' => $gitlabSuccessRequest,
                'bitbucket' => $bitbucketSuccessRequest,
            ]
        );
        $response->headers->set('Content-Type', 'text/plain');
        $response->setMaxAge(0);
        $response->setSharedMaxAge(0);

        return $response;
    }
}
