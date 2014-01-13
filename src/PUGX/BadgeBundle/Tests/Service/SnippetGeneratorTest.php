<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Simone Di Maulo <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests\Service;

use PUGX\BadgeBundle\Service\SnippetGenerator;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class SnippetGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider badgesRoutesProvider
     */
    public function testCompileRouteParametersForBadge($badges, $routes, $expectedParams)
    {
        $router = \Phake::mock('Symfony\Component\Routing\Router');
        \Phake::when($router)->getRouteCollection()->thenReturn($routes);

        $snippetGenerator = new SnippetGenerator($router, $badges);

        $reflectionMethod = new \ReflectionMethod($snippetGenerator, 'compileRouteParametersForBadge');
        $reflectionMethod->setAccessible(true);

        foreach ($badges as $i => $badge) {
            $parameters = $reflectionMethod->invokeArgs($snippetGenerator, array($badge));
            $this->assertEquals($expectedParams[$i], $parameters);
        }

    }

    public function badgesRoutesProvider()
    {
        $badges = array(
            array(
                'route' => 'pugx_badge_test_1',
                'name'  => 'Test Route #1',
                'parameter1' => 'value1',
                'parameter2' => 'value2'
            ),
            array(
                'route' => 'pugx_badge_test_2',
                'name'  => 'Test Route #2',
                'param1' => 'value#1',
                'param2' => 'value#2'
            ),
        );

        $routeTest1 = new Route(
            '/test1/{parameter1}/{parameter2}',
            array('parameter1' => '%d'),
            array('parameter2' => '%d')
        );

        $routeTest2 = new Route(
            '/test2/{parameter1}/{parameter2}',
            array('param2' => '%d','param1' => '%d')
        );

        $validRoutes = new RouteCollection();
        $validRoutes->add('pugx_badge_test_1', $routeTest1);
        $validRoutes->add('pugx_badge_test_2', $routeTest2);

        return array(
            array(
                $badges,
                $validRoutes,
                array(
                    array('parameter1' => 'value1', 'parameter2' => 'value2'),
                    array('param1' => 'value#1', 'param2' => 'value#2')
                )
            )
        );
    }

}
