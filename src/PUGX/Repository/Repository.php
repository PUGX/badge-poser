<?php

namespace PUGX\Repository;


class Repository {

    private $name;

    function __construct($name)
    {
        if (!$this->isValidRepositoryName($name)) {
            throw new \Exception("Repository Name is not valid");
        }

        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Validates a repository name.
     *
     * @param  string  $repository
     * @return Boolean
     */
    private function isValidRepositoryName($repository)
    {
        return (preg_match('/[A-Za-z0-9_.-]+\/[A-Za-z0-9_.-]+?/', $repository) === 1);
    }
}