<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\StatsBundle\Tests;

use PUGX\StatsBundle\Test\StatsFunctionalTest;
use Packagist\Api\Client;

class FunctionalControllerTest extends StatsFunctionalTest
{
    protected $packagistClient;

    // this setUp fake the request/response, if you comment this function the test'd run only with internet connection
    public function setUp()
    {
        // see the config_test.yml, there's different parameters and services.
        $data = '{"package":{"name":"pugx\/badge-poser","description":"add badges on your readme, such as downloads number or latest version.","time":"2013-05-24T14:45:06+00:00","maintainers":[{"name":"liuggio","email":"liuggio@gmail.com"}],"versions":{"dev-master":{"name":"pugx\/badge-poser","description":"add badges on your readme, such as downloads number or latest version.","keywords":[],"homepage":"","version":"dev-master","version_normalized":"9999999-dev","license":[],"authors":[],"source":{"type":"git","url":"https:\/\/github.com\/PUGX\/badge-poser.git","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e"},"dist":{"type":"zip","url":"https:\/\/api.github.com\/repos\/PUGX\/badge-poser\/zipball\/024df1d420cd715aea3400bfea9b87ed0f3bb47e","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e","shasum":""},"type":"library","time":"2013-05-24T19:19:25+00:00","autoload":{"psr-0":{"":"src\/"}},"extra":{"symfony-app-dir":"app","symfony-web-dir":"web","branch-alias":{"dev-master":"0.1-dev"}},"require":{"php":">=5.3.3","symfony\/symfony":"2.2.*","doctrine\/orm":">=2.2,<3.0,>=2.2.3","doctrine\/doctrine-bundle":"1.2.*","twig\/extensions":"1.0.*","symfony\/assetic-bundle":"2.1.*","symfony\/swiftmailer-bundle":"2.2.*","symfony\/monolog-bundle":"2.2.*","sensio\/distribution-bundle":"2.2.*","sensio\/framework-extra-bundle":"2.2.*","sensio\/generator-bundle":"2.2.*","jms\/security-extra-bundle":"1.4.*","jms\/di-extra-bundle":"1.3.*","knplabs\/packagist-api":"dev-master"},"require-dev":{"guzzle\/plugin-mock":"*"}},"dev-develop":{"name":"pugx\/badge-poser","description":"add badges on your readme, such as downloads number or latest version.","keywords":[],"homepage":"","version":"dev-develop","version_normalized":"dev-develop","license":[],"authors":[],"source":{"type":"git","url":"https:\/\/github.com\/PUGX\/badge-poser.git","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e"},"dist":{"type":"zip","url":"https:\/\/api.github.com\/repos\/PUGX\/badge-poser\/zipball\/024df1d420cd715aea3400bfea9b87ed0f3bb47e","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e","shasum":""},"type":"library","time":"2013-05-24T19:19:25+00:00","autoload":{"psr-0":{"":"src\/"}},"extra":{"symfony-app-dir":"app","symfony-web-dir":"web","branch-alias":{"dev-master":"0.1-dev"}},"require":{"php":">=5.3.3","symfony\/symfony":"2.2.*","doctrine\/orm":">=2.2,<3.0,>=2.2.3","doctrine\/doctrine-bundle":"1.2.*","twig\/extensions":"1.0.*","symfony\/assetic-bundle":"2.1.*","symfony\/swiftmailer-bundle":"2.2.*","symfony\/monolog-bundle":"2.2.*","sensio\/distribution-bundle":"2.2.*","sensio\/framework-extra-bundle":"2.2.*","sensio\/generator-bundle":"2.2.*","jms\/security-extra-bundle":"1.4.*","jms\/di-extra-bundle":"1.3.*","knplabs\/packagist-api":"dev-master"},"require-dev":{"guzzle\/plugin-mock":"*"}}},"type":"library","repository":"https:\/\/github.com\/PUGX\/badge-poser","downloads":{"total":99,"monthly":12,"daily":9},"favers":9}}';
        $this->packagistClient = $this->createPackagistClient($data, 200);
    }

    private function createPackagistClient($data, $status = 200)
    {
        $packagistResponse = new \Guzzle\Http\Message\Response($status);
        $packagistResponse->setBody($data);
        $plugin = new \Guzzle\Plugin\Mock\MockPlugin();
        $plugin->addResponse($packagistResponse);
        $clientHttp = new \Guzzle\Http\Client();
        $clientHttp->addSubscriber($plugin);

        return new Client($clientHttp);
    }

    public function testOnDownloadsActionStatisticShouldBeCreated()
    {
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->packagistClient);
        ob_start();
        $client->request('GET', '/pugx/badge-poser/d/total.svg');
        ob_clean();
        $this->checkStatsCalls($client, 'pugx/badge-poser', 'downloadsAction');
    }

    public function testOnLatestStableActionStatisticShouldBeCreated()
    {
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->packagistClient);
        ob_start();
        $client->request('GET', '/pugx/badge-poser/version.svg');
        ob_clean();
        $this->checkStatsCalls($client, 'pugx/badge-poser', 'versionAction');
    }

    public function testOnLatestUnstableActionStatisticShouldBeCreated()
    {
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->packagistClient);
        ob_start();
        $client->request('GET', '/pugx/badge-poser/v/unstable.svg');
        ob_clean();
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->checkStatsCalls($client, 'pugx/badge-poser', 'versionAction');
    }

    /**
     * @dataProvider provider
     */
    public function testOnHomeActionStatisticShouldNotBeCreated($path)
    {
        $client = static::createClient();
        $client->enableProfiler();

        $client->request('GET', $path);
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->checkStatsAreNotIncremented($client);
    }

    public function provider()
    {
        return array(
            array('/'),
            array('/show/pugx/badge-poser')
        );
    }
}
