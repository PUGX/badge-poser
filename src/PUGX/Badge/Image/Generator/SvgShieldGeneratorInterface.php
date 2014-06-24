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

/**
 * Interface SvgShieldGeneratorInterface
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 */
interface SvgShieldGeneratorInterface
{
    /**
     * @param string $vendor
     * @param string $type
     * @param string $color
     *
     * @return string
     */
    public function generateShield($vendor, $type, $color);
}
