<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests\Service;

use PUGX\BadgeBundle\Service\BucklerImageCreator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BucklerImageCreatorTest extends WebTestCase
{
    private $buckler;
    private $logger;
    private $normalizer;

    public function setUp()
    {
        $this->logger = \Phake::mock('Symfony\Bridge\Monolog\Logger');

        $this->buckler = new BucklerImageCreator($this->logger, 'buckler');
    }

    public function testCreateDownloadsImage()
    {
        $image = $this->buckler->createDownloadsImage('a');

        $this->assertInstanceOf('PUGX\BadgeBundle\BucklerImage', $image);

        $this->assertArrayHasKey('vendor', $image->get('array'));
        $this->assertArrayHasKey('status', $image->get('array'));
        $this->assertArrayHasKey('color', $image->get('array'));
    }
}
