<?php

namespace App\Stats\Persister;

/**
 * Interface PersisterInterface.
 */
interface PersisterInterface
{
    /**
     * Increment by one the total accesses.
     */
    public function incrementTotalAccess(): self;

    /**
     * Increment by one the repository accesses.
     */
    public function incrementRepositoryAccess(string $repository): self;

    /**
     * Add the repository to the list of the latest accessed.
     */
    public function addRepositoryToLatestAccessed(string $repository): self;

    /**
     * Increment by one the repository accesses type.
     */
    public function incrementRepositoryAccessType(string $repository, string $type): self;

    /**
     * Add the referrer to a subset.
     */
    public function addReferer(string $url): self;
}
