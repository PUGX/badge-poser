<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class CircleCiClient implements CircleCiClientInterface
{
    public function __construct(
        private UrlGeneratorInterface $router,
        private HttpClientInterface $httpClient,
        private string $circleToken,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function health(): ResponseInterface
    {
        $circleCiApiUrl = $this->router->generate('circleci_api_health');

        return $this->httpClient->request(
            Request::METHOD_GET,
            $circleCiApiUrl,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Circle-Token' => $this->circleToken,
                ],
            ]
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getBuilds(string $repository, string $branch = 'master'): ResponseInterface
    {
        $circleCiApiUrl = $this->router->generate('circleci_api_repository', ['repository' => $repository, 'branch' => \urlencode($branch)]);

        return $this->httpClient->request(
            Request::METHOD_GET,
            $circleCiApiUrl,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Circle-Token' => $this->circleToken,
                ],
                'query' => [
                    'filter' => 'completed',
                    'limit' => '1',
                ],
            ]
        );
    }
}
