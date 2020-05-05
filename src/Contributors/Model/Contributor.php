<?php

namespace App\Contributors\Model;

/**
 * Class Contributor.
 */
class Contributor
{
    private string $username;
    private string $profileUrl;
    private string $profileImg;

    private function __construct(string $username, string $profileUrl, string $profileImg)
    {
        $this->username = $username;
        $this->profileUrl = $profileUrl;
        $this->profileImg = $profileImg;
    }

    public static function create(string $username, string $profileUrl, string $profileImg): self
    {
        return new self($username, $profileUrl, $profileImg);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getProfileUrl(): string
    {
        return $this->profileUrl;
    }

    public function getProfileImg(): string
    {
        return $this->profileImg;
    }

    public function __toString()
    {
        return $this->username;
    }
}
