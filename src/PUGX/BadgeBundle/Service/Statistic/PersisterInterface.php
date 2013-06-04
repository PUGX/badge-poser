<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Service\Statistic;

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
     * @param int $maxListLenght
     *
     * @return PersisterInterface
     */
    public function addRepositoryToLatestAccessed($repository, $maxListLenght = 10);

    /**
     * Increment by one the repository accesses type.
     *
     * @param string $repository
     * @param string $type
     *
     * @return PersisterInterface
     */
    public function incrementRepositoryAccessType($repository, $type);
}