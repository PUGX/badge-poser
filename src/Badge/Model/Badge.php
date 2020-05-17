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
 * Class Badge.
 */
class Badge
{
    private const DEFAULT_FORMAT = 'svg';
    private string $subject;
    private string $status;
    private string $color;
    private string $format;

    public function __construct(string $subject, string $status, string $color, string $format = self::DEFAULT_FORMAT)
    {
        $this->subject = $this->escapeValue($subject);
        $this->status = $this->escapeValue($status);
        $this->format = $this->escapeValue($format);
        $this->color = $color;

        if (!$this->isValidColorHex($this->color)) {
            throw new \InvalidArgumentException(\sprintf('Color not valid %s', $this->color));
        }
    }

    /**
     * @return string the Hexadecimal #FFFFFF
     */
    public function getHexColor(): string
    {
        return '#'.$this->color;
    }

    /**
     * @return string the format of the image eg. `svg`.
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function __toString()
    {
        return \sprintf('%s-%s-%s.%s',
            $this->subject,
            $this->status,
            $this->color,
            $this->format
        );
    }

    private function escapeValue(string $value): string
    {
        $pattern = [
            // '/([^_])_([^_])/g', // damn it global doesn't work in PHP
            '/([^_])_$/',
            '/^_([^_])/',
            '/__/',
            '/--+/',
        ];
        $replacement = [
            //'$1 $2',
            '$1 ',
            ' $1',
            '°§*¼',
            '-',
        ];
        $ret = \preg_replace($pattern, $replacement, $value);
        $ret = \str_replace('_', ' ', $ret);    // this fix the php pgrep_replace is not global :(
        $ret = \str_replace('°§*¼', '_', $ret); // this fix the php pgrep_replace is not global :(

        return $ret;
    }

    /**
     * @return false|int
     */
    private function isValidColorHex(string $color)
    {
        $color = \ltrim($color, '#');
        $regex = '/^[0-9a-fA-F]{6}$/';

        return \preg_match($regex, $color);
    }
}
