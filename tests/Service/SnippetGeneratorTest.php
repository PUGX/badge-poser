<?php

namespace App\Tests\Service;

use App\Service\SnippetGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

class SnippetGeneratorTest extends TestCase
{
    public function testGenerateAllSnippets()
    {
        $router  = $this->prophesize(RouterInterface::class)
            ->reveal();

        $generator = new SnippetGenerator($router, [], []);

        $expected = [
            'clip_all' => [
                'markdown' => ''
            ],
            'repository' => [
                'html' => 'vendor/package',
            ],
        ];
        
        self::assertEquals($expected, $generator->generateAllSnippets('vendor/package'));
    }
}
