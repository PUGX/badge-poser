<?php

namespace App\Tests\Service;

use App\Service\SnippetGenerator;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SnippetGeneratorTest.
 */
class SnippetGeneratorTest extends TestCase
{
    use ProphecyTrait;

    public function testGenerateAllSnippets(): void
    {
        /** @var RouterInterface $router */
        $router = $this->prophesize(RouterInterface::class)
            ->reveal();

        $generator = new SnippetGenerator($router, [], []);

        $expected = [
            'all' => [
                'markdown' => '',
            ],
        ];

        self::assertEquals($expected, $generator->generateAllSnippets('vendor/package'));
    }
}
