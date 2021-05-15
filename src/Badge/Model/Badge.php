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

final class Badge implements BadgeInterface, \Stringable
{
    private const DEFAULT_FORMAT = 'svg';
    private string $subject;
    private string $status;
    private string $format;

    public function __construct(string $subject, string $status, private string $color, string $format = self::DEFAULT_FORMAT)
    {
        $this->subject = $this->escapeValue($subject);
        $this->status = $this->escapeValue($status);
        $this->format = $this->escapeValue($format);

        if (!$this->isValidColorHex($this->color)) {
            throw new \InvalidArgumentException(\sprintf('Color not valid %s', $this->color));
        }
    }

    /**
     * @return string the Hexadecimal color code e.g. "#FFFFFF"
     */
    public function getHexColor(): string
    {
        return '#'.$this->color;
    }

    /**
     * @return string the format of the image e.g. "svg".
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

    public function __toString(): string
    {
        return \sprintf(
            '%s-%s-%s.%s',
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
        if (null === $ret) {
            throw new \RuntimeException('Error while escaping');
        }

        // this fix the php pgrep_replace is not global :(

        return \str_replace(['_', '°§*¼'], [' ', '_'], $ret);
    }

    private function isValidColorHex(string $color): bool | int
    {
        $color = \ltrim($color, '#');
        $regex = '/^[0-9a-fA-F]{6}$/';

        return \preg_match($regex, $color);
    }
}
