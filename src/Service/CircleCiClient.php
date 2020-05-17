<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CircleCiClient implements CircleCiClientInterface
{
    protected UrlGeneratorInterface $router;

    protected HttpClientInterface $httpClient;

    protected string $circleToken;

    public function __construct(UrlGeneratorInterface $router, HttpClientInterface $httpClient, string $circleToken)
    {
        $this->router = $router;
        $this->httpClient = $httpClient;
        $this->circleToken = $circleToken;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getBuilds(string $repository, string $branch = 'master'): ResponseInterface
    {
        $circleCiApiUrl = $this->router->generate('circleci_api', ['repository' => $repository, 'branch' => \urlencode($branch)]);

        return $this->httpClient->request(
            Request::METHOD_GET,
            $circleCiApiUrl,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                ],
                'query' => [
                    'circle-token' => $this->circleToken,
                    'filter' => 'completed',
                    'limit' => '1',
                ],
            ]
        );
    }
}
