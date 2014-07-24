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
 * Class NullPersister
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class NullPersister implements PersisterInterface
{
    public static $incrementTotalAccessCalled = false;
    public static $incrementRepositoryAccessCalled = false;
    public static $addRepositoryToLatestAccessedCalled = false;
    public static $incrementRepositoryAccessTypeCalled = false;
    public static $addReferrer = false;

    /**
     * Increment by one the total accesses.
     *
     * @return PersisterInterface
     */
    public function incrementTotalAccess()
    {
        static::$incrementTotalAccessCalled = true;

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
        static::$incrementRepositoryAccessCalled = $repository;

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
        static::$incrementRepositoryAccessTypeCalled = array($repository, $type);

        return $this;
    }

    /**
     * Add the repository to the list of the latest accessed.
     *
     * @param string $repository
     * @param int    $maxListLength
     *
     * @return PersisterInterface
     */
    public function addRepositoryToLatestAccessed($repository, $maxListLength = 50)
    {
        static::$addRepositoryToLatestAccessedCalled = $repository;

        return $this;
    }

    /**
     * Add the referrer to a subset.
     *
     * @param string $url
     *
     * @return PersisterInterface
     */
    public function addReferer($url)
    {
        static::$addReferrer = $url;

        return $this;
    }

}
