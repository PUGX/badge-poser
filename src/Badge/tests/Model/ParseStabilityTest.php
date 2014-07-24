<?php

namespace PUGX\Badge\Package\Tests;

use Packagist\Api\Result\Package\Version;
use PUGX\Badge\Model\Package;

class ParseStabilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getVersionAndStability
     */
    public function testParseStability($version, $stable)
    {
        $this->assertEquals(Package::parseStability($version), $stable);
    }

    public static function getVersionAndStability()
    {
        return array(
            array('1.0.0', 'stable'),
            array('1.1.0', 'stable'),
            array('2.0.0', 'stable'),
            array('3.0.x-dev', 'dev'),
            array('v3.0.0-RC1', 'RC'),
            array('2.3.x-dev', 'dev'),
            array('2.2.x-dev', 'dev'),
            array('dev-master', 'dev'),
            array('2.1.x-dev', 'dev'),
            array('2.0.x-dev', 'dev'),
            array('v2.3.0-rc2', 'RC'),
            array('v2.3.0-RC1', 'RC'),
            array('v2.3.0-BETA2', 'beta'),
            array('v2.1.10', 'stable'),
            array('v2.2.1', 'stable'),
        );
    }

    protected function createVersion(array $branches)
    {
        $versions = array();
        foreach ($branches as $branch) {
            $version = new Version();
            $version->fromArray($branch);
            $versions[] = $version;
        }

        return $versions;
    }
}
