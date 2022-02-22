<?php

namespace App\Tests\Service;

use App\Service\SnippetGeneratorInterface;
use PUGX\Poser\Poser;

final class SnippetGeneratorStub implements SnippetGeneratorInterface
{
    public function generateAllSnippets(Poser $poser, string $repository): array
    {
        return [
            'all' => [
                'markdown' => 'sample markdown',
            ],
            'badges' => [
                [
                    'name' => 'latest_stable_version',
                    'label' => 'Latest Stable Version',
                    'markdown' => '[![Latest Stable Version](http://localhost/phpunit/phpunit/v)](https://packagist.org/packages/phpunit/phpunit)',
                    'img' => 'http://localhost/phpunit/phpunit/v',
                    'imgLink' => 'https://packagist.org/packages/phpunit/phpunit',
                    'featured' => true,
                ],
                [
                    'name' => 'latest_stable_version',
                    'label' => 'Latest Stable Version',
                    'markdown' => '[![Latest Stable Version](http://localhost/phpunit/phpunit/v)](https://packagist.org/packages/phpunit/phpunit)',
                    'img' => 'http://localhost/phpunit/phpunit/v',
                    'imgLink' => 'https://packagist.org/packages/phpunit/phpunit',
                    'featured' => false,
                ],
            ],
            'badge_styles' => [
                [
                    'name' => 'latest_stable_version',
                    'label' => 'flat',
                    'markdown' => '[![flat](http://poser.local:8001/phpunit/phpunit/v)](https://packagist.org/packages/phpunit/phpunit)',
                    'img' => 'http://poser.local:8001/phpunit/phpunit/v',
                    'imgLink' => 'https://packagist.org/packages/phpunit/phpunit',
                    'featured' => false,
                ],
                [
                    'name' => 'latest_stable_version',
                    'label' => 'for-the-badge',
                    'markdown' => '[![for-the-badge](http://poser.local:8001/phpunit/phpunit/v?style=for-the-badge)](https://packagist.org/packages/phpunit/phpunit)',
                    'img' => 'http://poser.local:8001/phpunit/phpunit/v?style=for-the-badge',
                    'imgLink' => 'https://packagist.org/packages/phpunit/phpunit',
                    'featured' => false,
                ],
            ],
        ];
    }
}
