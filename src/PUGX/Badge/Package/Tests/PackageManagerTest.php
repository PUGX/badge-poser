<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Package\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Packagist\Api\Result\Package\Version;
use PUGX\Badge\Package\PackageService;

class PackageManagerTest extends WebTestCase
{
    /**
     * @dataProvider getStableAndUnstableVersion
     */
    public function testPackageShouldContainStableAndUnstableVersion($stableAssertion, $unstableAssertion, array $versions)
    {
        $packagistClient = \Phake::mock('Packagist\Api\Client');
        $apiPackage = \Phake::mock('Packagist\Api\Result\Package');
        \Phake::when($apiPackage)->getVersions()->thenReturn($versions);
        \Phake::when($packagistClient)->get('puum')->thenReturn($apiPackage);

        $normalizer = $this->getMock('\PUGX\Badge\Package\TextNormalizer');
        $normalizer->expects($this->any())
            ->method('normalize')
            ->willReturnCallback(function ($in) {return $in;});

        $pm = new PackageService($packagistClient, '\PUGX\Badge\Package\Package', $normalizer);

        $package = $pm->fetchPackage('puum');

        $this->assertInstanceOf('PUGX\Badge\Package\Package', $package);
        $this->assertEquals($package->getLatestStableVersion(), $stableAssertion);
        $this->assertEquals($package->getLatestUnstableVersion(), $unstableAssertion);
    }

    public function getStableAndUnstableVersion()
    {
        return array(
            //    stable    unstable     versions
            array('v2.0.0', '3.1.0-dev', $this->createVersion(array(
                array('version' => 'v0.0.1',      'versionNormalized' => '0.0.1.0'),
                array('version' => 'v1.0.0',      'versionNormalized' => '1.0.0.0'),
                array('version' => 'v2.0.0',      'versionNormalized' => '2.0.0.0'),
                array('version' => '3.0.x-dev',   'versionNormalized' => '3.0.9999999.9999999-dev'),
                array('version' => 'v3.0.0-BETA1',  'versionNormalized' => '3.0.0.0-beta1'),
                array('version' => 'v3.0.0-RC1',  'versionNormalized' => '3.0.0.0-RC1'),
                array('version' => 'dev-master', 'versionNormalized' => '9999999-dev', 'extra'=>array('branch-alias'=>array('dev-master'=>'3.1.0-dev')))
            ))),

            array('v2.2.1', '2.3.x-dev', $this->createVersion(array(
                array('version' => '2.3.x-dev',     'versionNormalized' => '2.3.9999999.9999999-dev'),
                array('version' => '2.2.x-dev',     'versionNormalized' => '2.2.9999999.9999999-dev'),
                array('version' => '2.0.x-dev',     'versionNormalized' => '2.0.9999999.9999999-dev'),
                array('version' => 'v2.3.0-RC2',    'versionNormalized' => '2.3.0.0-RC2'),
                array('version' => 'v2.3.0-RC1',    'versionNormalized' => '2.3.0.0-RC1'),
                array('version' => 'v2.3.0-BETA2',  'versionNormalized' => '2.3.0.0-beta2'),
                array('version' => 'v2.1.10',       'versionNormalized' => '2.1.10.0'),
                array('version' => 'v2.2.1',        'versionNormalized' => '2.2.1.0'),
            ))),

            array('v1.10.0', 'dev-master', $this->createVersion(array(
                array('version' => 'v0.9.0',     'versionNormalized' => '0.9.0.0'),
                array('version' => 'v1.0.0',     'versionNormalized' => '1.0.0.'),
                array('version' => 'v1.9.0',     'versionNormalized' => '1.9.0.0'),
                array('version' => 'v1.10.0',    'versionNormalized' => '1.10.0.0'),
                array('version' => 'dev-master',    'versionNormalized' => '9999999-dev'),
            ))),
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
