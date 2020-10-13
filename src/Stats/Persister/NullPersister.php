<?php

namespace App\Stats\Persister;

/**
 * Class NullPersister.
 */
final class NullPersister implements PersisterInterface
{
    public static bool $incrementTotalAccessCalled = false;
    public static ?string $incrementRepositoryAccessCalled = null;
    public static ?string $addRepositoryToLatestAccessedCalled = null;
    /** @var array<int, string>|null */
    public static ?array $incrementRepositoryAccessTypeCalled = null;
    public static ?string $addReferrer = null;

    /**
     * Increment by one the total accesses.
     */
    public function incrementTotalAccess(): PersisterInterface
    {
        static::$incrementTotalAccessCalled = true;

        return $this;
    }

    /**
     * Increment by one the repository accesses.
     */
    public function incrementRepositoryAccess(string $repository): PersisterInterface
    {
        static::$incrementRepositoryAccessCalled = $repository;

        return $this;
    }

    /**
     * Increment by one the repository accesses type.
     */
    public function incrementRepositoryAccessType(string $repository, string $type): PersisterInterface
    {
        static::$incrementRepositoryAccessTypeCalled = [$repository, $type];

        return $this;
    }

    /**
     * Add the repository to the list of the latest accessed.
     */
    public function addRepositoryToLatestAccessed(string $repository): PersisterInterface
    {
        static::$addRepositoryToLatestAccessedCalled = $repository;

        return $this;
    }

    /**
     * Add the referrer to a subset.
     */
    public function addReferer(string $url): PersisterInterface
    {
        static::$addReferrer = $url;

        return $this;
    }
}
