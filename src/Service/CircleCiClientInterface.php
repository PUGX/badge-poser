<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface CircleCiClientInterface
{
    public function getBuilds(string $repository, string $branch = 'master'): ResponseInterface;
}
