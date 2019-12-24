<?php

namespace App\Tests\Badge\Model;

use App\Badge\Model\Package;
use Packagist\Api\Result\Package\Version;
use PHPUnit\Framework\TestCase;

/**
 * Class ParseStabilityTest.
 */
class ParseStabilityTest extends TestCase
{
    /**
     * @dataProvider getVersionAndStability
     */
    public function testParseStability(string $version, string $stable): void
    {
        $this->assertEquals(Package::parseStability($version), $stable);
    }

    public static function getVersionAndStability(): array
    {
        return [
            ['1.0.0', 'stable'],
            ['1.1.0', 'stable'],
            ['2.0.0', 'stable'],
            ['3.0.x-dev', 'dev'],
            ['v3.0.0-RC1', 'RC'],
            ['2.3.x-dev', 'dev'],
            ['2.2.x-dev', 'dev'],
            ['dev-master', 'dev'],
            ['2.1.x-dev', 'dev'],
            ['2.0.x-dev', 'dev'],
            ['v2.3.0-rc2', 'RC'],
            ['v2.3.0-RC1', 'RC'],
            ['v2.3.0-BETA2', 'beta'],
            ['v2.1.10', 'stable'],
            ['v2.2.1', 'stable'],
            ['0.1.0-alpha1', 'alpha'],
            ['0.1.0-alpha', 'alpha'],
        ];
    }

    protected function createVersion(array $branches): array
    {
        $versions = [];
        foreach ($branches as $branch) {
            $version = new Version();
            $version->fromArray($branch);
            $versions[] = $version;
        }

        return $versions;
    }
}
