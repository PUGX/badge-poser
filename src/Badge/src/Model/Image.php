<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Model;

/**
 * Class Image, an Image value Object
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class Image
{
    /** @var string */
    private $name;
    /** @var string */
    private $content;
    /** @var string */
    private $format;

    private function __construct($name, $content, $format)
    {
        $this->name = $name;
        $this->content = $content;
        $this->format = $format;
    }

    /**
     * Returns the image content as binary string
     */
    public function __toString()
    {
        return $this->content;
    }

    /**
     * @param $name
     * @param $content
     * @param  string $format
     * @return Image
     */
    public static function create($name, $content, $format = 'svg')
    {
        $content = (string) $content;

        return new self($name, $content, $format);
    }

    /**
     * Return the filename with file format.
     *
     * @return string
     */
    public function getOutputFileName()
    {
        return sprintf('%s.%s', $this->cleanName(), $this->format);
    }

    private function cleanName()
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $this->name);
        $clean = preg_replace("/[^a-zA-Z0-9_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '- '));

        return $clean;
    }
}
