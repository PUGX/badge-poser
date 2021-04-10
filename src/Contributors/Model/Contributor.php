<?php

namespace App\Contributors\Model;

final class Contributor implements \Stringable
{
    private const DEFAULT_IMG_SIZE = 160;

    private function __construct(private string $username, private string $profileUrl, private string $profileImg, private int $size = self::DEFAULT_IMG_SIZE)
    {
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

    public function __toString(): string
    {
        return $this->username;
    }
}
