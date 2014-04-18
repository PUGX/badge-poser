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
    const DAY = 'D';
    const MONTH = 'M';
    const YEAR = 'Y';
    /**
     * Read total accesses.
     *
     * @return integer
     */
    public function totalAccess();

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return mixed
     */

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $dimension
     *
     * @return mixed
     */
    public function totalDataOfAccessesByInterval(\DateTime $startDate, \DateTime $endDate, $dimension = ReaderInterface::MONTH);

    /**
     * @return string
     */
    public function getRandomRepository();
}
