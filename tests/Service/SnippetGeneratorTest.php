<?php

namespace App\Tests\Service;

use App\Service\SnippetGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

final class SnippetGeneratorTest extends TestCase
{
    public function testGenerateAllSnippetsWithFeaturedBadges(): void
    {
        $badges = [
            [
                'name' => 'badge_1_name',
                'label' => 'badge_1_lable',
                'route' => 'badge_1_route',
                'routeParam1' => 'param1Value',
                'routeParam3' => 'param3Value',
            ],
            [
                'name' => 'badge_2_name',
                'label' => 'badge_2_lable',
                'route' => 'badge_2_route',
                'routeParam1' => 'param1Value',
            ],
        ];

        $routeBadge1 = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routeBadge1->method('getDefaults')
            ->willReturn([
                'routeParam1' => 'param1DefValue',
                'routeParam2' => 'param2DefValue',
            ]);
        $routeBadge1->method('getRequirements')
            ->willReturn([
                'routeParam1' => '\d+',
                'routeParam3' => '.+',
            ]);

        $routeBadge2 = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routeBadge2->method('getDefaults')
            ->willReturn([
                'routeParam1' => 'param1DefValue',
            ]);
        $routeBadge2->method('getRequirements')
            ->willReturn([]);

        $routeCollection = $this->getMockBuilder(RouteCollection::class)
            ->getMock();
        $routeCollection->method('get')
            ->withConsecutive(
                ['badge_1_route'],
                ['badge_2_route'],
            )
            ->willReturnOnConsecutiveCalls($routeBadge1, $routeBadge2);

        $router = $this->getMockBuilder(RouterInterface::class)
            ->getMock();
        $router->method('getRouteCollection')
            ->willReturn($routeCollection);
        $router
            ->method('generate')
            ->withConsecutive(
                [
                    'pugx_badge_packagist',
                    ['repository' => 'vendor/package'],
                ],
                [
                    'badge_1_route',
                    ['routeParam1' => 'param1Value', 'routeParam3' => 'param3Value'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'badge_2_route',
                    ['routeParam1' => 'param1Value'],
                    RouterInterface::ABSOLUTE_URL,
                ],
            )
            ->willReturnOnConsecutiveCalls('repo_url', 'img_url', 'img_url2');

        $generator = new SnippetGenerator($router, ['badge_1_name', 'badge_2_name'], $badges);

        $expected = [
            'all' => [
                'markdown' => '[![badge_1_lable](img_url)](repo_url) [![badge_2_lable](img_url2)](repo_url)',
            ],
            'badges' => [
                [
                    'name' => 'badge_1_name',
                    'label' => 'badge_1_lable',
                    'markdown' => '[![badge_1_lable](img_url)](repo_url)',
                    'img' => 'img_url',
                    'imgLink' => 'repo_url',
                    'featured' => true,
                ],
                [
                    'name' => 'badge_2_name',
                    'label' => 'badge_2_lable',
                    'markdown' => '[![badge_2_lable](img_url2)](repo_url)',
                    'img' => 'img_url2',
                    'imgLink' => 'repo_url',
                    'featured' => true,
                ],
            ],
        ];

        self::assertEquals($expected, $generator->generateAllSnippets('vendor/package'));
    }

    public function testGenerateAllSnippetsWithoutFeaturedBadges(): void
    {
        $badges = [
            [
                'name' => 'badge_name',
                'label' => 'badge_lable',
                'route' => 'badge_route',
                'routeParam' => 'paramValue',
            ],
        ];

        $badgeRoute = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $badgeRoute->method('getDefaults')
            ->willReturn([
                'routeParam' => 'paramDefValue',
            ]);
        $badgeRoute->method('getRequirements')
            ->willReturn([]);

        $routeCollection = $this->getMockBuilder(RouteCollection::class)
            ->getMock();
        $routeCollection->method('get')
            ->with('badge_route')
            ->willReturn($badgeRoute);

        $router = $this->getMockBuilder(RouterInterface::class)
            ->getMock();
        $router->method('getRouteCollection')
            ->willReturn($routeCollection);
        $router
            ->method('generate')
            ->withConsecutive(
                [
                    'pugx_badge_packagist',
                    [
                        'repository' => 'vendor/package',
                    ],
                ],
                [
                    'badge_route',
                    [
                        'routeParam' => 'paramValue',
                    ],
                    RouterInterface::ABSOLUTE_URL,
                ],
            )
            ->willReturnOnConsecutiveCalls('repo_url', 'img_url');

        $generator = new SnippetGenerator($router, [], $badges);

        $expected = [
            'all' => [
                'markdown' => '',
            ],
            'badges' => [
                [
                    'name' => 'badge_name',
                    'label' => 'badge_lable',
                    'markdown' => '[![badge_lable](img_url)](repo_url)',
                    'img' => 'img_url',
                    'imgLink' => 'repo_url',
                    'featured' => false,
                ],
            ],
        ];

        self::assertEquals($expected, $generator->generateAllSnippets('vendor/package'));
    }

    public function testWontGenerateAllSnippetsIfBadgeRouteDoesNotExists(): void
    {
        $badges = [
            [
                'name' => 'badge_name',
                'label' => 'badge_lable',
                'route' => 'badge_route',
                'routeParam' => 'paramValue',
            ],
        ];

        $routeCollection = $this->getMockBuilder(RouteCollection::class)
            ->getMock();
        $routeCollection->method('get')
            ->with('badge_route')
            ->willReturn(null);

        $router = $this->getMockBuilder(RouterInterface::class)
            ->getMock();
        $router->method('getRouteCollection')
            ->willReturn($routeCollection);
        $router
            ->method('generate')
            ->with(
                'pugx_badge_packagist',
                [
                    'repository' => 'vendor/package',
                ],
            )
            ->willReturn('repo_url');

        $this->expectException(\RuntimeException::class);

        $generator = new SnippetGenerator($router, [], $badges);
        $generator->generateAllSnippets('vendor/package');
    }
}
