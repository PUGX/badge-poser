<?php

namespace App\Tests\Service;

use App\Service\SnippetGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SnippetGeneratorTest.
 */
class SnippetGeneratorTest extends TestCase
{
    public function testGenerateAllSnippets(): void
    {
        /** @var RouterInterface $router */
        $router = $this->createStub(RouterInterface::class);

        $generator = new SnippetGenerator($router, [], []);

        $expected = [
            'all' => [
                'markdown' => '',
            ],
        ];

        self::assertEquals($expected, $generator->generateAllSnippets('vendor/package'));
    }
}
