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

use Symfony\Component\HttpClient\HttpClient;
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

    /**
     * @param PackageRepositoryInterface $packageRepository
     */
    public function __construct(PackageRepositoryInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }
    /**
     * @param string $repository
     * @param string $format
     *
     * @return \PUGX\Badge\Model\Badge
     */
    public function createGitattributesBadge($repository, $format = 'svg')
    {
        $repo = str_replace('.git', '', $this->packageRepository
            ->fetchByRepository($repository)
            ->getOriginalObject()
            ->getRepository()
        );

        $client = HttpClient::create();

        $response = $client->request('HEAD', $repo . '/blob/master/.gitattributes', array(
            'timeout'         => 2,
            'connect_timeout' => 1,
            'exceptions'      => false,
        ));

        $status = $response->getStatusCode();

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
