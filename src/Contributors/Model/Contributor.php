<?php

namespace App\Contributors\Model;

/**
 * Class Contributor.
 */
class Contributor
{
    /** @var string */
    private $username;
    /** @var string */
    private $profileUrl;
    /** @var string */
    private $profileImg;

    private function __construct($username, $profileUrl, $profileImg)
    {
        $this->username = $username;
        $this->profileUrl = $profileUrl;
        $this->profileImg = $profileImg;
    }

    public static function create($username, $profileUrl, $profileImg): self
    {
        return new self($username, $profileUrl, $profileImg);
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getProfileUrl(): string
    {
        return $this->profileUrl;
    }

    /**
     * @return string
     */
    public function getProfileImg(): string
    {
        return $this->profileImg;
    }

    public function __toString()
    {
        return $this->username;
    }
}
