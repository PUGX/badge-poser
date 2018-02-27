<?php

namespace App\Stats\Persister;

interface PersisterInterface
{
    /**
     * Increment by one the total accesses.
     *
     * @return self
     */
    public function incrementTotalAccess(): self;

    /**
     * Increment by one the repository accesses.
     *
     * @param string $repository
     *
     * @return self
     */
    public function incrementRepositoryAccess(string $repository): self;

    /**
     * Add the repository to the list of the latest accessed.
     *
     * @param string $repository
     * @param int    $maxListLength
     *
     * @return self
     */
    public function addRepositoryToLatestAccessed(string $repository, int $maxListLength = 10): self;

    /**
     * Increment by one the repository accesses type.
     *
     * @param string $repository
     * @param string $type
     *
     * @return self
     */
    public function incrementRepositoryAccessType(string $repository, string $type): self;

    /**
     * Add the referrer to a subset.
     *
     * @param string $url
     *
     * @return self
     */
    public function addReferer(string $url): self;
}
