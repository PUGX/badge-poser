<?php

namespace App\Service;

/**
 * Interface SnippetGeneratorInterface.
 */
interface SnippetGeneratorInterface
{
    public function generateAllSnippets(string $repository): array;
}
