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

interface BadgeInterface
{
    public function getSubject(): string;

    public function getStatus(): string;

    public function getHexColor(): string;

    public function getStyle(): string;

    public function getFormat(): string;

    public function __toString(): string;
}
