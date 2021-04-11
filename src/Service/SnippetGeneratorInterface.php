<?php

namespace App\Service;

interface SnippetGeneratorInterface
{
    /**
     * @return array<string, array>
     */
    public function generateAllSnippets(string $repository): array;
}
