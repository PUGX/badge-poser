<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Controller\Badge;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DownloadsControllerTest.
 */
class DownloadsControllerTest extends WebTestCase
{
    public function testDownloadsAction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/downloads');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsTotalAction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/d/total');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsDailyAction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/d/daily');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsMonthlyAction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/d/monthly');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsActionSvgExplicit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/downloads.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsTotalActionSvgExplicit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pugx/badge-poser/d/total.svg');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDownloadsActionPngRedirectSvg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/pugx/badge-poser/downloads.png');
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('/pugx/badge-poser/downloads', $crawler->getUri());
    }
}
