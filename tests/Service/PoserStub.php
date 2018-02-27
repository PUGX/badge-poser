<?php

namespace App\Tests\Service;

use PUGX\Poser\Poser;
use PUGX\Poser\Render\SvgRender;

class PoserStub extends Poser
{

    public function generate($subject, $status, $color, $format)
    {
        return new SvgRender();
    }

    public function generateFromURI($string)
    {
        return new SvgRender();
    }

    public function validFormats()
    {
        return [];
    }

}