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
 * An Image value Object.
 */
final class Image implements \Stringable
{
    private function __construct(
        private readonly string $name,
        private readonly string $content,
        private readonly string $format,
    ) {
    }

    /**
     * Returns the image content as binary string.
     */
    public function __toString(): string
    {
        return $this->content;
    }

    public static function create(string $name, string $content, string $format = 'svg'): self
    {
        return new self($name, $content, $format);
    }

    /**
     * Return the filename with file format.
     */
    public function getOutputFileName(): string
    {
        return \sprintf('%s.%s', $this->cleanName(), $this->format);
    }

    private function cleanName(): string
    {
        $clean = \iconv('UTF-8', 'ASCII//TRANSLIT', $this->name);
        if (false === $clean) {
            throw new \RuntimeException('Error while parsing image name');
        }

        $clean = \preg_replace('/[^a-zA-Z0-9_|+ -]/', '', $clean);
        if (null === $clean) {
            throw new \RuntimeException('Error while parsing image name');
        }

        return \strtolower(\trim($clean, '- '));
    }
}
