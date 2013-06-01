<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
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
    private function instatiatePackageManager($versions = null)
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
            array('2.0.0', 'v3.0.0-RC1', array('1.0.0', '1.1.0', '2.0.0', '3.0.x-dev', 'v3.0.0-RC1')),
            array('v2.2.1', 'v2.3.0-RC2', array('2.3.x-dev', '2.2.x-dev', 'dev-master', '2.1.x-dev', '2.0.x-dev', 'v2.3.0-RC2', 'v2.3.0-RC1', 'v2.3.0-BETA2', 'v2.1.10', 'v2.2.1')),
            array(null, 'dev-master', array('dev-master')),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testPackageShouldContainStableAndUnstableVersion($stableAssertion, $unstableAssertion, $branches)
    {
        foreach ($branches as $branch) {
            $version = new Version();
            $version->fromArray(array('version' => $branch));
            $versions[] = $version;
        }

        $pm = $this->instatiatePackageManager($versions);
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
        $pm = $this->instatiatePackageManager();

        $this->assertEquals($pm->parseStability($version), $stable);

    }
}
