<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Badge\Model;

/**
 * Class Image
 * An Image value Object.
 */
class Image
{
    private string $name;
    private string $content;
    private string$format;

    private function __construct(string $name, string $content, string $format)
    {
        $this->name = $name;
        $this->content = $content;
        $this->format = $format;
    }

    /**
     * Returns the image content as binary string.
     */
    public function __toString()
    {
        return $this->content;
    }

    /**
     * @return Image
     */
    public static function create(string $name, string $content, string $format = 'svg'): self
    {
        $content = (string) $content;

        return new self($name, $content, $format);
    }

    /**
     * Return the filename with file format.
     */
    public function getOutputFileName(): string
    {
        return sprintf('%s.%s', $this->cleanName(), $this->format);
    }

    private function cleanName(): string
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $this->name);
        $clean = preg_replace('/[^a-zA-Z0-9_|+ -]/', '', $clean);
        $clean = strtolower(trim($clean, '- '));

        return $clean;
    }
}
