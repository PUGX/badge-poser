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

class Badge
{
    CONST DEFAULT_FORMAT = 'svg';
    private $subject;
    private $status;
    private $color;
    private $format;

    public function __construct($subject, $status, $color, $format = self::DEFAULT_FORMAT)
    {
        $this->subject = $this->escapeValue($subject);
        $this->status  = $this->escapeValue($status);
        $this->format  = $this->escapeValue($format);
        $this->color   = $color;

        if (!$this->isValidColorHex($this->color)) {
            throw new \InvalidArgumentException(sprintf('Color not valid %s', $this->color));
        }
    }

    /**
     * @return string the Hexadecimal #FFFFFF.
     */
    public function getHexColor()
    {
        return '#'.$this->color;
    }

    /**
     * @return string the format of the image eg. `svg`.
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    public function __toString()
    {
        return sprintf("%s-%s-%s.%s",
            $this->subject,
            $this->status,
            $this->color,
            $this->format
        );
    }

    private function escapeValue($value)
    {
        $pattern = array(
            // '/([^_])_([^_])/g', // damn it global doesn't work in PHP
            '/([^_])_$/',
            '/^_([^_])/',
            '/__/',
            '/--+/',
        );
        $replacement = array(
            //'$1 $2',
            '$1 ',
            ' $1',
            '°§*¼',
            '-',
        );
        $ret = preg_replace($pattern, $replacement, $value);
        $ret = str_replace('_', ' ', $ret);    // this fix the php pgrep_replace is not global :(
        $ret = str_replace('°§*¼', '_', $ret); // this fix the php pgrep_replace is not global :(

        return $ret;
    }


    private function isValidColorHex($color)
    {
        $color = ltrim($color, "#");
        $regex = '/^[0-9a-fA-F]{6}$/';

        return preg_match($regex, $color);
    }
}
