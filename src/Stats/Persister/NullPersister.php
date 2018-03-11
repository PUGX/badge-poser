<?php

namespace App\Stats\Persister;

/**
 * Class NullPersister
 * @package App\Stats\Persister
 */
final class NullPersister implements PersisterInterface
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
    public function incrementTotalAccess(): PersisterInterface
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
    public function incrementRepositoryAccess($repository): PersisterInterface
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
    public function incrementRepositoryAccessType(string $repository, string $type): PersisterInterface
    {
        static::$incrementRepositoryAccessTypeCalled = [$repository, $type];

        return $this;
    }

    /**
     * Add the repository to the list of the latest accessed.
     *S
     * @param string $repository
     * @param int    $maxListLength
     *
     * @return PersisterInterface
     */
    public function addRepositoryToLatestAccessed(string $repository, int $maxListLength = 50): PersisterInterface
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
    public function addReferer(string $url): PersisterInterface
    {
        static::$addReferrer = $url;

        return $this;
    }

}
