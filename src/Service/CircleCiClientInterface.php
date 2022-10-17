<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface CircleCiClientInterface
{
    public function health(): ResponseInterface;

    public function getBuilds(string $repository, string $branch = 'master'): ResponseInterface;
}
