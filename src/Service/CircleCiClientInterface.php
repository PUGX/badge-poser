<?php

namespace App\Service;

use Psr\Http\Message\ResponseInterface;

interface CircleCiClientInterface
{
    public function getBuilds(string $repository, string $branch = 'master'): ResponseInterface;
}
