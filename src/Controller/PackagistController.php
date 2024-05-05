<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Packagist\Api\Client as PackagistClient;
use Packagist\Api\Result\Result as PackagistResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class PackagistController extends AbstractController
{
    #[Route('/search_packagist', name: 'search_packagist', methods: 'GET')]
    public function search(Request $request, PackagistClient $packagistClient): JsonResponse
    {
        $responseContent = [];
        $packageName = (string) $request->query->get('name');
        $pages = $request->query->getInt('pages', 1);

        $packagistResponse = $packagistClient->search($packageName, [], $pages);

        /** @var PackagistResult $package */
        foreach ($packagistResponse as $package) {
            $responseContent[] = ['id' => $package->getName(), 'description' => $package->getDescription()];
        }

        return new JsonResponse($responseContent);
    }
}
