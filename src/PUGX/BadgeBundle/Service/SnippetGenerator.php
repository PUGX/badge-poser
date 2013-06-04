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

class SnippetGenerator
{

    private $router;
    private $routes;

    public function __construct(Router $router, $routes)
    {
        $this->router = $router;
        $this->routes = $routes;

    }

    /**
     * @param $repository
     * @return array
     */
    public function generateAllSnippets($repository)
    {
        $snippets = array();
        foreach($this->routes as $route) {

            $routeName = $route['name'];
            $snippets[$routeName] = array(
                'markdown'  => $this->generateMarkdown($route, $repository),
                'img'       => $this->generateImg($route, $repository)
            );
        }
        return $snippets;
    }

    /**
     * @param $route
     * @param $repository
     * @return string
     */
    public function generateMarkdown($route, $repository)
    {
        return sprintf(
            "[![%s](%s)](%s)",
            $route['label'],
            $this->generateImg($route, $repository),
            $this->generateRepositoryLink($repository)
        );

    }

    /**
     * @param $route
     * @param $repository
     * @return string
     */
    public function generateImg($route, $repository)
    {
        return $this->router->generate($route['route'], array('repository' => $repository), true);
    }

    /**
     * @param $repository
     * @return string
     */
    public function generateRepositoryLink($repository)
    {
        return sprintf('https://packagist.org/packages/%s', $repository);
    }
}