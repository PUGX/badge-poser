<?php

namespace App\Controller;

use App\Service\SnippetGeneratorInterface;
use GuzzleHttp\Exception\BadResponseException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SnippetController extends AbstractController
{
    /**
     * Returns all snippets for a package.
     *
     * @throws \Exception
     */
    public function all(Request $request, SnippetGeneratorInterface $generator): JsonResponse
    {
        $repository = $request->get('repository');
        $response = new JsonResponse();

        if (!$this->isValidRepositoryName($repository)) {
            $response->setData(['msg' => 'Package not found. Please check the package name. e.g. (symfony/symfony)']);
            $response->setStatusCode(404);

            return $response;
        }

        try {
            $badges = $generator->generateAllSnippets($repository);
            $response->setData($badges);
        } catch (BadResponseException) {
            $response->setData(['msg' => 'Package not found. Please check the package name. e.g. (symfony/symfony)']);
            $response->setStatusCode(404);
        } catch (\Exception $e) {
            $response->setData(['msg' => 'Server Error']);
            $response->setStatusCode(500);
            throw $e;
        }

        // cache for 3600 seconds
        $response->setSharedMaxAge(3600);

        return $response;
    }

    /**
     * Validates a repository name.
     */
    private function isValidRepositoryName(string $repository): bool
    {
        return 1 === \preg_match('/[A-Za-z0-9_.-]+\/[A-Za-z0-9_.-]+?/', $repository);
    }
}
