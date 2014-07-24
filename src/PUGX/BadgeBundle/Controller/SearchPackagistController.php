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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SearcPackagistController.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Leonardo Proietti <leonardo.proietti@gmail.com>
 * @author Simone Fumagalli <simone@iliveinperego.com>
 * @author Andrea Giuliano <giulianoand@gmail.com>
 */
class SearchPackagistController extends ContainerAware
{
    /**
     * @Route("/search_packagist", name="search_packagist")
     * @Method("GET")
     */
    public function searchPackagistAction(Request $request)
    {
        $responseContent = array();
        $packageName = $request->query->get('name');

        $packagistResponse = $this->container->get('packagist_client')->search($packageName);

        foreach ($packagistResponse as $package) {
            $responseContent[] = array("id" => $package->getName(), "description" => $package->getDescription());
        }

        $response = new JsonResponse($responseContent);

        return $response;
    }
}
