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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Packagist\Api\Result\Package\Version;
use PUGX\BadgeBundle\Service\PackageManager;

class PackageManagerTest extends WebTestCase
{
    private function instantiatePackageManager($versions = null)
    {
        $packagistClient = $this->getMock('Packagist\Api\Client');

        if (null !== $versions) {
            $apiPackage = $this->getMock('Packagist\Api\Result\Package');
            $apiPackage->expects($this->once())
                ->method('getVersions')
                ->will($this->returnValue($versions));

            $packagistClient->expects($this->once())
                ->method('get')
                ->will($this->returnValue($apiPackage));
        }

        $pm = new PackageManager($packagistClient, '\PUGX\BadgeBundle\Package\Package');

        return $pm;
    }

    public static function provider()
    {
        return array(
            //    stable    unstable     versions
            array('v2.0.0', '3.1.0-dev', array(
                array('version' => 'v0.0.1',      'versionNormalized' => '0.0.1.0'),
                array('version' => 'v1.0.0',      'versionNormalized' => '1.0.0.0'),
                array('version' => 'v2.0.0',      'versionNormalized' => '2.0.0.0'),
                array('version' => '3.0.x-dev',   'versionNormalized' => '3.0.9999999.9999999-dev'),
                array('version' => 'v3.0.0-BETA1',  'versionNormalized' => '3.0.0.0-beta1'),
                array('version' => 'v3.0.0-RC1',  'versionNormalized' => '3.0.0.0-RC1'),
                array('version' => 'dev-master', 'versionNormalized' => '9999999-dev', 'extra'=>array('branch-alias'=>array('dev-master'=>'3.1.0-dev')))
            )),

            array('v2.2.1', '2.3.x-dev', array(
                array('version' => '2.3.x-dev',     'versionNormalized' => '2.3.9999999.9999999-dev'),
                array('version' => '2.2.x-dev',     'versionNormalized' => '2.2.9999999.9999999-dev'),
                array('version' => '2.0.x-dev',     'versionNormalized' => '2.0.9999999.9999999-dev'),
                array('version' => 'v2.3.0-RC2',    'versionNormalized' => '2.3.0.0-RC2'),
                array('version' => 'v2.3.0-RC1',    'versionNormalized' => '2.3.0.0-RC1'),
                array('version' => 'v2.3.0-BETA2',  'versionNormalized' => '2.3.0.0-beta2'),
                array('version' => 'v2.1.10',       'versionNormalized' => '2.1.10.0'),
                array('version' => 'v2.2.1',        'versionNormalized' => '2.2.1.0'),
            )),

            array('v1.10.0', 'dev-master', array(
                array('version' => 'v0.9.0',     'versionNormalized' => '0.9.0.0'),
                array('version' => 'v1.0.0',     'versionNormalized' => '1.0.0.'),
                array('version' => 'v1.9.0',     'versionNormalized' => '1.9.0.0'),
                array('version' => 'v1.10.0',    'versionNormalized' => '1.10.0.0'),
                array('version' => 'dev-master',    'versionNormalized' => '9999999-dev'),
            )),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testPackageShouldContainStableAndUnstableVersion($stableAssertion, $unstableAssertion, $branches)
    {
        foreach ($branches as $branch) {
            $version = new Version();
            $version->fromArray($branch);
            $versions[] = $version;
        }

        $pm = $this->instantiatePackageManager($versions);
        $package = $pm->fetchPackage('puum');
        $pm->calculateLatestVersions($package);

        $this->assertInstanceOf('PUGX\BadgeBundle\Package\PackageInterface', $package);
        $this->assertEquals($package->getLatestStableVersion(), $stableAssertion);
        $this->assertEquals($package->getLatestUnstableVersion(), $unstableAssertion);
    }

    public static function stabilityProvider()
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

    /**
     * @dataProvider stabilityProvider
     */
    public function testParseStability($version, $stable)
    {
        $pm = $this->instantiatePackageManager();

        $this->assertEquals($pm->parseStability($version), $stable);

    }
}
