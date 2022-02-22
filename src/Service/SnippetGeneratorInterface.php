<?php

namespace App\Service;

use PUGX\Poser\Poser;

interface SnippetGeneratorInterface
{
    /**
     * @return array<string, array>
     */
    public function generateAllSnippets(Poser $poser, string $repository): array;
}
