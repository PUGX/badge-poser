<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\StatsBundle\Service;

/**
 * Class PersisterInterface
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
Interface PersisterInterface
{
    /**
     * Increment by one the total accesses.
     *
     * @return PersisterInterface
     */
    public function incrementTotalAccess();

    /**
     * Increment by one the repository accesses.
     *
     * @param string $repository
     *
     * @return PersisterInterface
     */
    public function incrementRepositoryAccess($repository);

    /**
     * Add the repository to the list of the latest accessed.
     *
     * @param string $repository
     * @param int    $maxListLength
     *
     * @return PersisterInterface
     */
    public function addRepositoryToLatestAccessed($repository, $maxListLength = 10);

    /**
     * Increment by one the repository accesses type.
     *
     * @param string $repository
     * @param string $type
     *
     * @return PersisterInterface
     */
    public function incrementRepositoryAccessType($repository, $type);

    /**
     * Add the referrer to a subset.
     *
     * @param string $url
     *
     * @return PersisterInterface
     */
    public function addReferer($url);
}
