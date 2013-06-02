<?php

/*
 * This file is part of the badge-poser package
 *
 * (c) Simone Di Maulo <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SnippetController extends ContainerAware
{
    /**
     * @Route("/snippet/all/",
     *     name="pugx_snippet_all"
     *     )
     * @Method({"GET"})
     * @Cache(smaxage="3600")
     * @return JsonResponse
     */
    public function allAction()
    {
        $data = array();
        $status = 500;
        $name = $this->container->get('request')->get('username');
        $repo = $this->container->get('request')->get('repository');
        
        if($name != null || $repo != null) {
            $data = array(
            'total' => array(
                'markdown' => '[![Total Downloads](https://poser.pugx.org/'.$name.'/'.$repo.'/d/total.png)](https://packagist.org/packages/symfony/symfony)',
                'img'      => 'https://poser.pugx.org/'.$name.'/'.$repo.'/d/total.png',
            ));
            $status = 200;
        }

        return new JsonResponse($data, $status);
    }
}
