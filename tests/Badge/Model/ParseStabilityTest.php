<?php

namespace App\Tests\Badge\Model;

use Packagist\Api\Result\Package\Version;
use App\Badge\Model\Package;
use PHPUnit\Framework\TestCase;

/**
 * Class ParseStabilityTest
 * @package App\Tests\Badge\Model
 */
class ParseStabilityTest extends TestCase
{
    /**
     * @dataProvider getVersionAndStability
     * @param $version
     * @param $stable
     */
    public function testParseStability($version, $stable): void
    {
        $this->assertEquals(Package::parseStability($version), $stable);
    }

    public static function getVersionAndStability(): array
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
            array('0.1.0-alpha1', 'alpha'),
            array('0.1.0-alpha', 'alpha'),
        );
    }

    protected function createVersion(array $branches): array
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
