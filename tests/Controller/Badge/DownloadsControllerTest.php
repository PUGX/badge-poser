<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Basge\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DownloadsControllerTest
 * @package App\Tests\Basge\Controller
 */
class DownloadsControllerTest extends WebTestCase
{
    public function testDownloadsAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/downloads');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsTotalAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/d/total');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsDailyAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/d/daily');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsMonthlyAction()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/d/monthly');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsActionSvgExplicit()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/downloads.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsTotalActionSvgExplicit()
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/d/total.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsActionPngRedirectSvg()
    {
        $client = static::createClient();

        $client->request('GET', '/pugx/badge-poser/downloads.png');
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('/pugx/badge-poser/downloads', $crawler->getUri());
    }
}
