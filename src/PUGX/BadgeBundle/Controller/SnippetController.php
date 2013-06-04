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
        $username = $this->container->get('request')->get('username');
        $repository = $this->container->get('request')->get('repository');
        $repository = sprintf('%s/%s', $username, $repository);

        return new JsonResponse($this->container->get('snippet_generator')->generateAllSnippets($repository));
    }
}
