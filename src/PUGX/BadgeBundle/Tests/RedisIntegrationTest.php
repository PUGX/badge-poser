<?php

namespace PUGX\BadgeBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RedisIntegrationTest extends WebTestCase
{
    public function setUp()
    {
       if ( ! class_exists('\Redis')) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the use of Redis');
       }
    }

    public function testRedisShouldBeCalledCorrectly()
    {
        $redis = new \Redis();
        $this->assertTrue($redis->connect('localhost'));
    }

    public function testRedisShouldWorkProperly()
    {
        $redis = new \Redis();
        $redis->connect('localhost');

        $this->assertTrue($redis->set('test', 'test'), 'The set method return false');
        $this->assertEquals('test', $redis->get('test'), 'The get method it\'s broken');
    }
}
