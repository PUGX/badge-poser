<?php


namespace App\Tests\Contributors\Model;


use App\Contributors\Model\Contributor;
use PHPUnit\Framework\TestCase;

class ContributorTest extends TestCase
{
    public function testCreation()
    {
        $contributor = Contributor::create('JellyBellyDev', 'http://profileUrl', 'http://profileImg');

        $this->assertInstanceOf(Contributor::class, $contributor);
        $this->assertEquals('JellyBellyDev', $contributor->getUsername());
        $this->assertEquals('http://profileUrl', $contributor->getProfileUrl());
        $this->assertEquals('http://profileImg', $contributor->getProfileImg());
    }

    public function testToString()
    {
        $contributor = Contributor::create('JellyBellyDev', 'http://profileUrl', 'http://profileImg');

        $this->assertEquals('JellyBellyDev', (string) $contributor);
    }
}