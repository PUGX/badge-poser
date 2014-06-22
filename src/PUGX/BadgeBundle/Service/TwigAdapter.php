<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace PUGX\BadgeBundle\Service;

use PUGX\Badge\Image\Template\TemplateEngineInterface;

/**
 * Class TwigAdapter
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 */
class TwigAdapter implements TemplateEngineInterface
{
    /**
     * @var \Twig_Environment $twig
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $templatePath
     * @param array  $parameters
     *
     * @return string
     */
    public function render($templatePath, array $parameters)
    {
        return $this->twig->render($templatePath, $parameters);
    }
}
