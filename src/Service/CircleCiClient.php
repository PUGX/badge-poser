<?php


namespace App\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CircleCiClient implements CircleCiClientInterface
{

    /** @var UrlGeneratorInterface */
    protected $router;

    /** @var ClientInterface */
    protected $client;

    /** @var string */
    protected $circleToken;

    /**
     * @param UrlGeneratorInterface $router
     * @param ClientInterface $client
     * @param string $circleToken
     */
    public function __construct(UrlGeneratorInterface $router, ClientInterface $client, string $circleToken)
    {
        $this->router = $router;
        $this->client = $client;
        $this->circleToken = $circleToken;
    }

    /**
     * @param string $repository
     * @param string $branch
     * @return ResponseInterface
     * @throws RouteNotFoundException
     * @throws MissingMandatoryParametersException
     * @throws InvalidParameterException
     * @throws GuzzleException
     */
    public function getBuilds(string $repository, string $branch = 'master'): ResponseInterface
    {

        $circleCiApiUrl = $this->router->generate('circleci_api', ['repository' => $repository, 'branch' => urlencode($branch)]);

        return $this->client->request(
            'GET',
            $circleCiApiUrl,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ],
                'query' => [
                    'circle-token' => $this->circleToken,
                    'filter' => 'completed',
                    'limit' => '1',
                ]
            ]
        );

    }
}