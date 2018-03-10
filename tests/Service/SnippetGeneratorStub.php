<?php

namespace App\Tests\Service;

use App\Service\SnippetGeneratorInterface;

/**
 * Class SnippetGeneratorStub
 * @package App\Tests\Service
 */
class SnippetGeneratorStub implements SnippetGeneratorInterface
{
    public function generateAllSnippets(string $repository): array
    {
        return ['all snippets for '.$repository];
    }

    public function generateMarkdown(array $badge, string $repository): string
    {
        // TODO: Implement generateMarkdown() method.
    }

    public function generateImg(array $badge, string $repository): string
    {
        // TODO: Implement generateImg() method.
    }

    public function generateRepositoryLink(string $repository): string
    {
        // TODO: Implement generateRepositoryLink() method.
    }
}
