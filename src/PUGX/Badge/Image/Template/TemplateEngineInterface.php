<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Image\Template;

/**
 * Interface TemplateEngineInterface
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 */
interface TemplateEngineInterface
{
    /**
     * @param string $templatePath
     * @param array  $parameters
     *
     * @return string
     */
    public function render($templatePath, array $parameters);
}
