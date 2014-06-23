<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace PUGX\Badge\Image\Generator;

use PUGX\Badge\Image\Template\TemplateEngineInterface;

/**
 * Class SvgShieldGenerator
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 */
class SvgShieldGenerator implements SvgShieldGeneratorInterface
{
    const STRING_SIZE    = 11;
    const STRING_FONT    = '/../Font/DejaVuSans.ttf';
    const VENDOR_COLOR   = '#555';
    const SHIELD_PADDING = 17;

    private static $colorScheme = array(
        "brightgreen" => "#4c1",
        "green"       => "#97CA00",
        "yellow"      => "#dfb317",
        "yellowgreen" => "#a4a61d",
        "orange"      => "#fe7d37",
        "red"         => "#e05d44",
        "blue"        => "#007ec6",
        "grey"        => "#555",
        "lightgray"   =>  "#9f9f9f"
    );

    /**
     * @var TemplateEngineInterface $templateEngine
     */
    private $templateEngine;

    /**
     * @var string $template
     */
    private $template;

    /**
     * @param TemplateEngineInterface $templateEngine
     * @param string                  $template
     */
    public function __construct(TemplateEngineInterface $templateEngine, $template)
    {
        $this->templateEngine = $templateEngine;
        $this->template       = $template;
    }

    /**
     * @param string $vendor
     * @param string $value
     * @param string $color
     *
     * @return string
     */
    public function generateShield($vendor, $value, $color)
    {
        $parameters = array();

        $color = array_key_exists($color, self::$colorScheme) ? self::$colorScheme[$color] : $color;

        $parameters['vendorWidth']         = $this->stringWidth($vendor);
        $parameters['valueWidth']          = $this->stringWidth($value);
        $parameters['totalWidth']          = $parameters['valueWidth'] + $parameters['vendorWidth'];
        $parameters['vendorColor']         = self::VENDOR_COLOR;
        $parameters['valueColor']          = $color;
        $parameters['vendor']              = $vendor;
        $parameters['value']               = $value;
        $parameters['vendorStartPosition'] = round($parameters['vendorWidth'] / 2, 1) + 1;
        $parameters['valueStartPosition']  = $parameters['vendorWidth'] + round($parameters['valueWidth'] / 2, 1) - 1;

        return $this->templateEngine->render($this->template, $parameters);
    }

    /**
     * @param string $text
     * @param string $font
     * @param int    $size
     *
     * @return int
     */
    private function stringWidth($text, $font = self::STRING_FONT, $size = self::STRING_SIZE)
    {
        $size = $this->convertToPt($size);
        $box  = imageftbbox($size, 0, __DIR__ . $font, $text);

        return round($box[2] - $box[0] + self::SHIELD_PADDING,  1);
    }

    /**
     * @param int $pixels
     *
     * @return int
     */
    private function convertToPt($pixels)
    {
        return round($pixels * 0.75, 1);
    }
}
