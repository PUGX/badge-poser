<?php

namespace App\Tests\Service;

use App\Service\SnippetGenerator;
use PHPUnit\Framework\TestCase;
use PUGX\Poser\Poser;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

final class SnippetGeneratorTest extends TestCase
{
    public function testGenerateAllSnippetsWithAndWithoutFeaturedBadges(): void
    {
        $routePugxBadgeVersionLatest = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routePugxBadgeVersionLatest->method('getDefaults')
            ->willReturn([
                'latest' => 'stable',
                'style' => 'flat',
                '_ext' => 'svg',
            ]);
        $routePugxBadgeVersionLatest->method('getRequirements')
            ->willReturn([
                'latest' => 'stable|unstable',
                'repository' => '[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?',
                'style' => 'flat|flat-square|for-the-badge|plastic',
                '_ext' => 'svg',
            ]);

        $routePugxBadgeVersionLatestUnstable = $routePugxBadgeVersionLatest;

        $routePugxBadgeDownload = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routePugxBadgeDownload->method('getDefaults')
            ->willReturn([
                'type' => 'total',
                'style' => 'flat',
                '_ext' => 'svg',
            ]);
        $routePugxBadgeDownload->method('getRequirements')
            ->willReturn([
                'repository' => '[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?',
                'style' => 'flat|flat-square|for-the-badge|plastic',
                '_ext' => 'svg',
            ]);

        $routePugxBadgeLicense = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routePugxBadgeLicense->method('getDefaults')
            ->willReturn([
                'style' => 'flat',
                '_ext' => 'svg',
            ]);
        $routePugxBadgeLicense->method('getRequirements')
            ->willReturn([
                'repository' => '[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?',
                'style' => 'flat|flat-square|for-the-badge|plastic',
                '_ext' => 'svg',
            ]);

        $routepugxBadgeDownloadTypeMonthly = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routepugxBadgeDownloadTypeMonthly->method('getDefaults')
            ->willReturn([
                'style' => 'flat',
                '_ext' => 'svg',
            ]);
        $routepugxBadgeDownloadTypeMonthly->method('getRequirements')
            ->willReturn([
                'type' => 'total|daily|monthly',
                'repository' => '[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?',
                'style' => 'flat|flat-square|for-the-badge|plastic',
                '_ext' => 'svg',
            ]);

        $routepugxBadgeDownloadTypeDaily = $routepugxBadgeDownloadTypeMonthly;

        $routePugxBadgeVersion = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routePugxBadgeVersion->method('getDefaults')
            ->willReturn([
                'latest' => 'stable',
                'style' => 'flat',
                '_ext' => 'svg',
            ]);
        $routePugxBadgeVersion->method('getRequirements')
            ->willReturn([
                'repository' => '[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?',
                'style' => 'flat|flat-square|for-the-badge|plastic',
                '_ext' => 'svg',
            ]);

        $routePugxBadgeRequire = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routePugxBadgeRequire->method('getDefaults')
            ->willReturn([
                'style' => 'flat',
                '_ext' => 'svg',
            ]);
        $routePugxBadgeRequire->method('getRequirements')
            ->willReturn([
                'repository' => '[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?',
                'type' => '.+',
                'style' => 'flat|flat-square|for-the-badge|plastic',
                '_ext' => 'svg',
            ]);

        $routePugxBadgeComposerlock = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routePugxBadgeComposerlock->method('getDefaults')
            ->willReturn([
                'style' => 'flat',
                '_ext' => 'svg',
            ]);
        $routePugxBadgeComposerlock->method('getRequirements')
            ->willReturn([
                'repository' => '[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?',
                'style' => 'flat|flat-square|for-the-badge|plastic',
                '_ext' => 'svg',
            ]);

        $routePugxBadgeGitAttributes = $routePugxBadgeDependents = $routePugxBadgeSuggesters = $routePugxBadgeComposerlock;

        $routePugxBadgeCircleci = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routePugxBadgeCircleci->method('getDefaults')
            ->willReturn([
                'branch' => 'master',
                'style' => 'flat',
                '_ext' => 'svg',
            ]);
        $routePugxBadgeCircleci->method('getRequirements')
            ->willReturn([
                'repository' => '[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?',
                'branch' => '.+',
                'style' => 'flat|flat-square|for-the-badge|plastic',
                '_ext' => 'svg',
            ]);

        $routeCollection = $this->getMockBuilder(RouteCollection::class)
            ->getMock();
        $routeCollection->method('get')
            ->withConsecutive(
                ['pugx_badge_version_latest'],
            )
            ->willReturnOnConsecutiveCalls(
                $routePugxBadgeVersionLatest,
                $routePugxBadgeDownload,
                $routePugxBadgeVersionLatestUnstable,
                $routePugxBadgeLicense,
                $routepugxBadgeDownloadTypeMonthly,
                $routepugxBadgeDownloadTypeDaily,
                $routePugxBadgeVersion,
                $routePugxBadgeRequire,
                $routePugxBadgeComposerlock,
                $routePugxBadgeGitAttributes,
                $routePugxBadgeDependents,
                $routePugxBadgeSuggesters,
                $routePugxBadgeCircleci
            );

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
                    'pugx_badge_version_latest',
                    ['latest' => 'stable', 'repository' => 'vendor/package'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_download',
                    ['repository' => 'vendor/package'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_version_latest',
                    ['latest' => 'unstable', 'repository' => 'vendor/package'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_license',
                    ['repository' => 'vendor/package'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_download_type',
                    ['repository' => 'vendor/package', 'type' => 'monthly'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_download_type',
                    ['repository' => 'vendor/package', 'type' => 'daily'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_version',
                    ['repository' => 'vendor/package'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_require',
                    ['repository' => 'vendor/package', 'type' => 'php'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_composerlock',
                    ['repository' => 'vendor/package'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_gitattributes',
                    ['repository' => 'vendor/package'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_dependents',
                    ['repository' => 'vendor/package'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_suggesters',
                    ['repository' => 'vendor/package'],
                    RouterInterface::ABSOLUTE_URL,
                ],
                [
                    'pugx_badge_circleci',
                    ['repository' => 'vendor/package'],
                    RouterInterface::ABSOLUTE_URL,
                ]
            )
            ->willReturnOnConsecutiveCalls('repo_url', 'img_url0', 'img_url1', 'img_url2', 'img_url3', 'img_url4', 'img_url5', 'img_url6', 'img_url7', 'img_url8', 'img_url9', 'img_url10', 'img_url11', 'img_url12');

        $poser = $this->getMockBuilder(Poser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $generator = new SnippetGenerator($router);

        $expected = [
            'all' => [
                'markdown' => '[![Latest Stable Version](img_url0)](repo_url) [![Total Downloads](img_url1)](repo_url) [![Latest Unstable Version](img_url2)](repo_url) [![License](img_url3)](repo_url) [![PHP Version Require](img_url7)](repo_url)',
            ],
            'badges' => [
                [
                    'name' => 'latest_stable_version',
                    'label' => 'Latest Stable Version',
                    'markdown' => '[![Latest Stable Version](img_url0)](repo_url)',
                    'img' => 'img_url0',
                    'imgLink' => 'repo_url',
                    'featured' => true,
                ],
                [
                    'name' => 'total',
                    'label' => 'Total Downloads',
                    'markdown' => '[![Total Downloads](img_url1)](repo_url)',
                    'img' => 'img_url1',
                    'imgLink' => 'repo_url',
                    'featured' => true,
                ],
                [
                    'name' => 'latest_unstable_version',
                    'label' => 'Latest Unstable Version',
                    'markdown' => '[![Latest Unstable Version](img_url2)](repo_url)',
                    'img' => 'img_url2',
                    'imgLink' => 'repo_url',
                    'featured' => true,
                ],
                [
                    'name' => 'license',
                    'label' => 'License',
                    'markdown' => '[![License](img_url3)](repo_url)',
                    'img' => 'img_url3',
                    'imgLink' => 'repo_url',
                    'featured' => true,
                ],
                [
                    'name' => 'monthly',
                    'label' => 'Monthly Downloads',
                    'markdown' => '[![Monthly Downloads](img_url4)](repo_url)',
                    'img' => 'img_url4',
                    'imgLink' => 'repo_url',
                    'featured' => false,
                ],
                [
                    'name' => 'daily',
                    'label' => 'Daily Downloads',
                    'markdown' => '[![Daily Downloads](img_url5)](repo_url)',
                    'img' => 'img_url5',
                    'imgLink' => 'repo_url',
                    'featured' => false,
                ],
                [
                    'name' => 'version',
                    'label' => 'Version',
                    'markdown' => '[![Version](img_url6)](repo_url)',
                    'img' => 'img_url6',
                    'imgLink' => 'repo_url',
                    'featured' => false,
                ],
                [
                    'name' => 'require_php',
                    'label' => 'PHP Version Require',
                    'markdown' => '[![PHP Version Require](img_url7)](repo_url)',
                    'img' => 'img_url7',
                    'imgLink' => 'repo_url',
                    'featured' => true,
                ],
                [
                    'name' => 'composerlock',
                    'label' => 'composer.lock',
                    'markdown' => '[![composer.lock](img_url8)](repo_url)',
                    'img' => 'img_url8',
                    'imgLink' => 'repo_url',
                    'featured' => false,
                ],
                [
                    'name' => 'gitattributes',
                    'label' => '.gitattributes',
                    'markdown' => '[![.gitattributes](img_url9)](repo_url)',
                    'img' => 'img_url9',
                    'imgLink' => 'repo_url',
                    'featured' => false,
                ],
                [
                    'name' => 'dependents',
                    'label' => 'Dependents',
                    'markdown' => '[![Dependents](img_url10)](repo_url)',
                    'img' => 'img_url10',
                    'imgLink' => 'repo_url',
                    'featured' => false,
                ],
                [
                    'name' => 'suggesters',
                    'label' => 'Suggesters',
                    'markdown' => '[![Suggesters](img_url11)](repo_url)',
                    'img' => 'img_url11',
                    'imgLink' => 'repo_url',
                    'featured' => false,
                ],
                [
                    'name' => 'circleci',
                    'label' => 'CircleCI Build',
                    'markdown' => '[![CircleCI Build](img_url12)](repo_url)',
                    'img' => 'img_url12',
                    'imgLink' => 'repo_url',
                    'featured' => false,
                ],
            ],
        ];

        self::assertEquals($expected, $generator->generateAllSnippets($poser, 'vendor/package'));
    }

    public function testWontGenerateAllSnippetsIfBadgeRouteDoesNotExists(): void
    {
        $routeCollection = $this->getMockBuilder(RouteCollection::class)
            ->getMock();
        $routeCollection->method('get')
            ->with('pugx_badge_version_latest')
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

        $poser = $this->getMockBuilder(Poser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->expectException(\RuntimeException::class);

        $generator = new SnippetGenerator($router);
        $generator->generateAllSnippets($poser, 'vendor/package');
    }
}
