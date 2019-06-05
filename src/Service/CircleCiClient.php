<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CircleCiClient implements CircleCiClientInterface
{
    /** @var UrlGeneratorInterface */
    protected $router;

    /** @var HttpClientInterface */
    protected $httpClient;

    /** @var string */
    protected $circleToken;

    /**
     * @param UrlGeneratorInterface $router
     * @param HttpClientInterface   $httpClient
     * @param string                $circleToken
     */
    public function __construct(UrlGeneratorInterface $router, HttpClientInterface $httpClient, string $circleToken)
    {
        $this->router = $router;
        $this->httpClient = $httpClient;
        $this->circleToken = $circleToken;
    }

    /**
     * @param string $repository
     * @param string $branch
     *
     * @return ResponseInterface
     *
     * @throws TransportExceptionInterface
     */
    public function getBuilds(string $repository, string $branch = 'master'): ResponseInterface
    {
        $circleCiApiUrl = $this->router->generate('circleci_api', ['repository' => $repository, 'branch' => urlencode($branch)]);

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
