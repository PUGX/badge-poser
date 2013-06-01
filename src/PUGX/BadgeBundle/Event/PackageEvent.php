<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PackageEvent extends Event
{
    CONST ACTION_DOWNLOAD = 'download';

    protected $package;
    protected $action;

    public function __construct($package, $action = self::ACTION_DOWNLOAD)
    {
        $this->package = $package;
        $this->action = $action;
    }

    public function getPackage()
    {
        return $this->package;
    }
}