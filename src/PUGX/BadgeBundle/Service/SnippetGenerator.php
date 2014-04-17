<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Simone Di Maulo <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Service;

use PUGX\Repository\Repository;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class SnippetGenerator
 *
 * @author Simone Di Maulo <toretto460@gmail.com>
 */
class SnippetGenerator
{
    /**
     * @var Router $router
     */
    private $router;

    /**
     * @var array $badges
     */
    private $badges;

    /**
     * @var array $allInBadges
     */
    private $allInBadges;

    /**
     * @var null|RouteCollection
     */
    private $routes;

    /**
     * @var string $packagistRoute
     */
    private $packagistRoute;

    /**
     * @param Router $router
     * @param array $badges
     * @param array $allInBadges
     * @param string $packagistRoute
     */
    public function __construct(Router $router, array $badges, array $allInBadges, $packagistRoute = 'pugx_badge_packagist')
    {
        $this->router = $router;
        $this->badges = $badges;
        $this->allInBadges = $allInBadges;
        $this->packagistRoute = $packagistRoute;
        $this->routes = $this->router->getRouteCollection();
    }

    /**
     * @param Repository $repository
     * @return array
     */
    public function generateAllSnippets(Repository $repository)
    {
        $snippets = array();
        $snippets['clip_all']['markdown'] = '';
        foreach ($this->badges as $badge) {
            $markdown = $this->generateMarkdown($badge, $repository);
            $snippets[$badge['name']] = array(
                'markdown'  => $markdown,
                'img'       => $this->generateImg($badge, $repository)
            );

            if (in_array($badge['name'], $this->allInBadges)) {
                $snippets['clip_all']['markdown'] .= ' '.$markdown;
            }
        }
        $snippets['clip_all']['markdown'] = trim($snippets['clip_all']['markdown']);
        $snippets['repository']['html'] = (string) $repository;

        return $snippets;
    }

    /**
     * @param $badge
     * @param Repository $repository
     * @return string
     */
    public function generateMarkdown($badge, Repository $repository)
    {
        return sprintf(
            "[![%s](%s)](%s)",
            $badge['label'],
            $this->generateImg($badge, $repository),
            $this->generateRepositoryLink($repository)
        );
    }

    /**
     * @param $badge
     * @param Repository $repository
     * @return string
     */
    public function generateImg($badge, Repository $repository)
    {
        $badge['repository'] = (string) $repository;
        $parameters = $this->compileRouteParametersForBadge($badge);

        return $this->router->generate($badge['route'], $parameters, true);
    }

    /**
     * @param Repository $repository
     * @return string
     */
    public function generateRepositoryLink(Repository $repository)
    {
        return $this->router->generate($this->packagistRoute, array('repository' => (string) $repository), true);
    }

    /**
     * @param $badge
     * @return array
     */
    private function compileRouteParametersForBadge($badge)
    {
        $parameters = array();
        $route = $this->routes->get($badge['route']);
        $routeParameters = array_keys(array_merge($route->getDefaults(), $route->getRequirements()));

        foreach ($routeParameters as $routeParameter) {
            if (array_key_exists($routeParameter, $badge)) {
                $parameters[$routeParameter] = $badge[$routeParameter];
            }
        }

        return $parameters;
    }
}
