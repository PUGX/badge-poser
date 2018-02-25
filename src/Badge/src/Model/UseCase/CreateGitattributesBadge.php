<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PUGX\Badge\Model\UseCase;

use Guzzle\Http\Client;
use PUGX\Badge\Model\PackageRepositoryInterface;

/**
 * Create the 'gitattributes' image using a generator `Poser`
 */
class CreateGitattributesBadge extends BaseCreatePackagistImage
{
    const COLOR_COMMITTED            = '96d490';
    const COLOR_UNCOMMITTED          = 'ad6c4b';
    const COLOR_ERROR                = 'aa0000';
    const GITATTRIBUTES_COMMITTED    = 'committed';
    const GITATTRIBUTES_UNCOMMITTED  = 'uncommitted';
    const GITATTRIBUTES_ERROR        = 'checking';
    const SUBJECT                    = '.gitattributes';
    const SUBJECT_ERROR              = 'Error';

    protected $text = self::GITATTRIBUTES_ERROR;

    /** @var Client */
    protected $client;

    /**
     * @param PackageRepositoryInterface $packageRepository
     * @param Client $client
     */
    public function __construct(PackageRepositoryInterface $packageRepository, Client $client)
    {
        $this->packageRepository = $packageRepository;
        $this->client = $client;
    }
    /**
     * @param string $repository
     * @param string $format
     *
     * @return \PUGX\Badge\Model\Badge
     */
    public function createGitattributesBadge($repository, $format = 'svg')
    {
        try {
            $repo = str_replace('.git', '', $this->packageRepository
                ->fetchByRepository($repository)
                ->getOriginalObject()
                ->getRepository()
            );
        } catch (\Exception $e) {
            return $this->createDefaultBadge( $format);
        }

        $request = $this->client->head(
            $repo . '/blob/master/.gitattributes',
            array(),
            array(
                'timeout'         => 2,
                'connect_timeout' => 1,
                'exceptions'      => false,
            )
        );

        $response = $this->client->send($request);
        $status = 500;
        if ($request) {
            $status = $response->getStatusCode();
        }

        $this->text = self::GITATTRIBUTES_ERROR;
        $color      = self::COLOR_ERROR;
        $subject    = self::SUBJECT_ERROR;
        if (200 === $status) {
            $this->text = self::GITATTRIBUTES_COMMITTED;
            $color      = self::COLOR_COMMITTED;
            $subject    = self::SUBJECT;
        } elseif (404 === $status) {
            $this->text = self::GITATTRIBUTES_UNCOMMITTED;
            $color      = self::COLOR_UNCOMMITTED;
            $subject    = self::SUBJECT;
        }

        return $this->createBadgeFromRepository(
            $repository,
            $subject,
            $color,
            $format
        );
    }

    protected function prepareText($package, $context = null)
    {
        return $this->text;
    }
}
