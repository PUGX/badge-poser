<?php

namespace App\Tests\Badge\Model;

use App\Badge\Model\Package;
use Packagist\Api\Result\Package\Version;
use PHPUnit\Framework\TestCase;

final class ParseStabilityTest extends TestCase
{
    /** @dataProvider getVersionAndStability */
    public function testParseStability(string $version, string $stable): void
    {
        self::assertEquals(Package::parseStability($version), $stable);
    }

    /**
     * @return \Generator<array<int, string>>
     */
    public static function getVersionAndStability(): \Generator
    {
        yield ['1.0.0', 'stable'];
        yield ['1.1.0', 'stable'];
        yield ['2.0.0', 'stable'];
        yield ['3.0.x-dev', 'dev'];
        yield ['v3.0.0-RC1', 'RC'];
        yield ['2.3.x-dev', 'dev'];
        yield ['2.2.x-dev', 'dev'];
        yield ['dev-master', 'dev'];
        yield ['2.1.x-dev', 'dev'];
        yield ['2.0.x-dev', 'dev'];
        yield ['v2.3.0-rc2', 'RC'];
        yield ['v2.3.0-RC1', 'RC'];
        yield ['v2.3.0-BETA2', 'beta'];
        yield ['v2.1.10', 'stable'];
        yield ['v2.2.1', 'stable'];
        yield ['0.1.0-alpha1', 'alpha'];
        yield ['0.1.0-alpha', 'alpha'];
    }

    /**
     * @param array<int, array> $branches
     *
     * @return array<int, Version>
     */
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
