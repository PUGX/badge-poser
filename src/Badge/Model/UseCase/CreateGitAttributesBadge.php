<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Badge\Model\UseCase;

use App\Badge\Model\Badge;
use App\Badge\Model\PackageRepositoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;

/**
 * Create the 'gitattributes' image using a generator `Poser`
 */
class CreateGitAttributesBadge extends BaseCreatePackagistImage
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

    /** @var PackageRepositoryInterface */
    protected $packageRepository;

    /** @var ClientInterface */
    protected $client;

    /**
     * @param PackageRepositoryInterface $packageRepository
     * @param ClientInterface Client $client
     */
    public function __construct(PackageRepositoryInterface $packageRepository, ClientInterface $client)
    {
        parent::__construct($packageRepository);
        $this->client = $client;
    }

    /**
     * @param string $repository
     * @param string $format
     *
     * @return Badge
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createGitAttributesBadge($repository, $format = 'svg'): Badge
    {
        $repo = str_replace('.git', '', $this->packageRepository
            ->fetchByRepository($repository)
            ->getOriginalObject()
            ->getRepository()
        );

        $response = $this->client->request(
            'HEAD',
            $repo . '/blob/master/.gitattributes',
            [
                RequestOptions::TIMEOUT => 2,
                RequestOptions::CONNECT_TIMEOUT => 1,
                RequestOptions::HTTP_ERRORS => false,
            ]
        );

        $status = 500;
        if ($response) {
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

    /**
     * @param $package
     * @param null $context
     * @return string
     */
    protected function prepareText($package, $context = null): string
    {
        return $this->text;
    }
}
