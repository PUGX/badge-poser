<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\StatsBundle\Service;

/**
 * Class ReaderInterface
 *
 * @author Simone Fumagalli <simone@iliveinperego.com>
 */
Interface ReaderInterface
{
    /**
     * Read total accesses.
     *
     * @return integer
     */
    public function totalAccess();

}
