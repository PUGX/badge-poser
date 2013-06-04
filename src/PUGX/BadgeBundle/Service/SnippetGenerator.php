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

use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouteCollection;

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
     * @var null|RouteCollection
     */
    private $routes;

    /**
     * @var string $packagistRoute
     */
    private $packagistRoute;

    /**
     * @param Router $router
     * @param $badges
     * @param string $packagist_route
     */
    public function __construct(Router $router, $badges, $packagist_route = 'pugx_badge_packagist')
    {
        $this->router = $router;
        $this->badges = $badges;
        $this->packagistRoute = $packagist_route;
        $this->routes = $this->router->getRouteCollection();
    }

    /**
     * @param $repository
     * @return array
     */
    public function generateAllSnippets($repository)
    {
        $snippets = array();
        foreach($this->badges as $badge) {

            $snippets[$badge['name']] = array(
                'markdown'  => $this->generateMarkdown($badge, $repository),
                'img'       => $this->generateImg($badge, $repository)
            );
        }
        return $snippets;
    }

    /**
     * @param $badge
     * @param $repository
     * @return string
     */
    public function generateMarkdown($badge, $repository)
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
     * @param $repository
     * @return string
     */
    public function generateImg($badge, $repository)
    {
        $badge['repository'] = $repository;
        $parameters = $this->compileRouteParametersForBadge($badge);

        return $this->router->generate($badge['route'], $parameters, true);
    }

    /**
     * @param $repository
     * @return string
     */
    public function generateRepositoryLink($repository)
    {
        return $this->router->generate($this->packagistRoute, array('repository' => $repository), true);
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

        foreach( $routeParameters as $routeParameter ){
            if (array_key_exists($routeParameter, $badge)) {
                $parameters[$routeParameter] = $badge[$routeParameter];
            }
        }

        return $parameters;
    }
}