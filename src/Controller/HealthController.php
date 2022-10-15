<?php

namespace App\Controller;

use App\Service\CircleCiClientInterface;
use App\Service\GitLabClientInterface;
use Bitbucket\Client as BitbucketClient;
use Packagist\Api\Client as PackagistClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class HealthController extends AbstractController
{
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

        // sample repo taken from https://github.com/PUGX/badge-poser/issues/337
        $bitbucketHealth = $bitbucketClient->repositories()->workspaces('wirbelwild')->show('kiwa-core');
        $bitbucketSuccessRequest = !empty($bitbucketHealth) && !empty($bitbucketHealth['full_name']);

        $response = $this->render(
            'health/index.txt.twig',
            [
                'packagist' => $packagistSuccessRequest ? 'OK' : 'KO',
                'circleci' => $circleciSuccessRequest ? 'OK' : 'KO',
                'gitlab' => $gitlabSuccessRequest ? 'OK' : 'KO',
                'bitbucket' => $bitbucketSuccessRequest ? 'OK' : 'KO',
            ]
        );
        $response->headers->set('Content-Type', 'text/plain');
        $response->setMaxAge(0);
        $response->setSharedMaxAge(0);

        return $response;
    }
}
