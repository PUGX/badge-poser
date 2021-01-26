<?php

namespace App\Contributors\Model;

/**
 * Class Contributor.
 */
class Contributor
{
    private const DEFAULT_IMG_SIZE = 160;

    private string $username;
    private string $profileUrl;
    private string $profileImg;
    private int $size;

    private function __construct(string $username, string $profileUrl, string $profileImg, int $size = self::DEFAULT_IMG_SIZE)
    {
        $this->username = $username;
        $this->profileUrl = $profileUrl;
        $this->profileImg = $profileImg;
        $this->size = $size;
    }

    public static function create(string $username, string $profileUrl, string $profileImg, int $size = self::DEFAULT_IMG_SIZE): self
    {
        return new self($username, $profileUrl, $profileImg, $size);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getProfileUrl(): string
    {
        return $this->profileUrl;
    }

    private function getUrlSeparator(string $url): string
    {
        return \strpos($url, '?')
               ? '&'
               : '?';
    }

    public function getProfileImg(): string
    {
        $sep = $this->getUrlSeparator($this->profileImg);
        $qs = \http_build_query(['s' => $this->size]);

        return $this->profileImg.$sep.$qs;
    }

    public function __toString()
    {
        return $this->username;
    }
}
