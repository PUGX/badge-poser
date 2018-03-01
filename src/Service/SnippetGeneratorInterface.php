<?php

namespace App\Service;

/**
 * Class SnippetGenerator
 *
 * @author Simone Di Maulo <toretto460@gmail.com>
 */
interface SnippetGeneratorInterface
{
    public function generateAllSnippets(string $repository): array;

    public function generateMarkdown(array $badge, string $repository): string;

    public function generateImg(array $badge, string $repository): string;

    public function generateRepositoryLink(string $repository): string;
}
