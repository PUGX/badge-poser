<?php

namespace App\Service;

/**
 * Interface SnippetGeneratorInterface.
 */
interface SnippetGeneratorInterface
{
    /**
     * @return array<string, array>
     */
    public function generateAllSnippets(string $repository): array;
}
