<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\StatsBundle\Service;

class NullPersister implements PersisterInterface
{
    /**
     * Increment by one the total accesses.
     *
     * @return PersisterInterface
     */
    public function incrementTotalAccess()
    {
        return $this;
    }

    /**
     * Increment by one the repository accesses.
     *
     * @param string $repository
     *
     * @return PersisterInterface
     */
    public function incrementRepositoryAccess($repository)
    {
      return $this;
    }

    /**
     * Increment by one the repository accesses type.
     *
     * @param string $repository
     * @param string $type
     *
     * @return PersisterInterface
     */
    public function incrementRepositoryAccessType($repository, $type)
    {
        return $this;
    }

    /**
     * Add the repository to the list of the latest accessed.
     *
     * @param string $repository
     * @param int $maxListLength
     *
     * @return PersisterInterface
     */
    public function addRepositoryToLatestAccessed($repository, $maxListLength = 50)
    {
        return $this;
    }
}