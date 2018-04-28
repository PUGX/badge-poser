<?php

namespace App\Tests\Service;

use App\Service\SnippetGeneratorInterface;

/**
 * Class SnippetGeneratorStub.
 */
class SnippetGeneratorStub implements SnippetGeneratorInterface
{
    public function generateAllSnippets(string $repository): array
    {
        return [
            'all' => [
                'markdown' => 'sample markdown'
            ],
            'badges' => [
                [
                    'name' => 'latest_stable_version',
                    'label' => 'Latest Stable Version',
                    'markdown' => '[![Latest Stable Version](http://localhost/phpunit/phpunit/v)](https://packagist.org/packages/phpunit/phpunit)',
                    'img' => 'http://localhost/phpunit/phpunit/v',
                    'featured' => true
                ],
                [
                    'name' => 'latest_stable_version',
                    'label' => 'Latest Stable Version',
                    'markdown' => '[![Latest Stable Version](http://localhost/phpunit/phpunit/v)](https://packagist.org/packages/phpunit/phpunit)',
                    'img' => 'http://localhost/phpunit/phpunit/v',
                    'featured' => false
                ]
            ]
        ];
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
