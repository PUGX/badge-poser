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
use Packagist\Api\Result\Package;
use Packagist\Api\Client;
use PUGX\Badge\Package\PackageService;

class LatestVersionOnPackageManagerTest extends WebTestCase
{
     private function createPM($data, $status = 200)
     {
        $packagistResponse = new \Guzzle\Http\Message\Response($status);
        $packagistResponse->setBody($data);
        $plugin = new \Guzzle\Plugin\Mock\MockPlugin();
        $plugin->addResponse($packagistResponse);
        $clientHttp = new \Guzzle\Http\Client();
        $clientHttp->addSubscriber($plugin);

        $c = new Client($clientHttp);
        $normalizer = $this->getMock('\PUGX\Badge\Package\TextNormalizer');
        $normalizer->expects($this->any())
            ->method('normalize')
            ->willReturnCallback(function ($in) {return $in;});

        return new PackageService($c, '\PUGX\Badge\Package\Package', $normalizer);
    }

    public static function provider()
    {
        return array(
            //    stable    unstable     json
            array('v2.3.1', '2.4-dev', __DIR__ . '/fixtures/packages/symfony.json'),
            // @todo mmm shouldn't be dev-master but dev-develop!
            array('v0.11.0', 'dev-master', __DIR__ . '/fixtures/packages/imagine-bundle.json'),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testPackageShouldContainStableAndUnstableVersion($stableAssertion, $unstableAssertion, $file)
    {
        $data = file_get_contents($file);

        $pm = $this->createPM($data, 200);
        $package = $pm->fetchPackage('a/a');

        $this->assertInstanceOf('PUGX\Badge\Package\Package', $package);
        $this->assertEquals($package->getLatestStableVersion(), $stableAssertion);
        $this->assertEquals($package->getLatestUnstableVersion(), $unstableAssertion);
    }
}
