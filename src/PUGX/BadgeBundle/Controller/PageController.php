<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class PageController
 *
 * @author Giorgio Cefaro <giorgio.cefaro@gmail.com>
 */
class PageController extends ContainerAware
{
    /**
     * @Route("/",
     *     name="pugx_page_home"
     *     )
     * @Method({"GET"})
     * @Template
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @return Response
     */
    public function homeAction()
    {
        return array();
    }
}
