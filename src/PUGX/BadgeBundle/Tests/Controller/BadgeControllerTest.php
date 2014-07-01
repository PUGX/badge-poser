<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests\Controller;

use Packagist\Api\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BadgeControllerTest extends WebTestCase
{
    protected $packagistClient;

    // this setUp fake the request/response, if you comment this function the test'd run only with internet connection
    public function setUp()
    {
        // see the config_test.yml, there's different parameters and services.
        $data = '{"package":{"name":"pugx\/badge-poser","description":"add badges on your readme, such as downloads number or latest version.","time":"2013-05-24T14:45:06+00:00","maintainers":[{"name":"liuggio","email":"liuggio@gmail.com"}],"versions":{"dev-master":{"name":"pugx\/badge-poser","description":"add badges on your readme, such as downloads number or latest version.","keywords":[],"homepage":"","version":"dev-master","version_normalized":"9999999-dev","license":[],"authors":[],"source":{"type":"git","url":"https:\/\/github.com\/PUGX\/badge-poser.git","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e"},"dist":{"type":"zip","url":"https:\/\/api.github.com\/repos\/PUGX\/badge-poser\/zipball\/024df1d420cd715aea3400bfea9b87ed0f3bb47e","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e","shasum":""},"type":"library","time":"2013-05-24T19:19:25+00:00","autoload":{"psr-0":{"":"src\/"}},"extra":{"symfony-app-dir":"app","symfony-web-dir":"web","branch-alias":{"dev-master":"0.1-dev"}},"require":{"php":">=5.3.3","symfony\/symfony":"2.2.*","doctrine\/orm":">=2.2,<3.0,>=2.2.3","doctrine\/doctrine-bundle":"1.2.*","twig\/extensions":"1.0.*","symfony\/assetic-bundle":"2.1.*","symfony\/swiftmailer-bundle":"2.2.*","symfony\/monolog-bundle":"2.2.*","sensio\/distribution-bundle":"2.2.*","sensio\/framework-extra-bundle":"2.2.*","sensio\/generator-bundle":"2.2.*","jms\/security-extra-bundle":"1.4.*","jms\/di-extra-bundle":"1.3.*","knplabs\/packagist-api":"dev-master"},"require-dev":{"guzzle\/plugin-mock":"*"}},"dev-develop":{"name":"pugx\/badge-poser","description":"add badges on your readme, such as downloads number or latest version.","keywords":[],"homepage":"","version":"dev-develop","version_normalized":"dev-develop","license":[],"authors":[],"source":{"type":"git","url":"https:\/\/github.com\/PUGX\/badge-poser.git","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e"},"dist":{"type":"zip","url":"https:\/\/api.github.com\/repos\/PUGX\/badge-poser\/zipball\/024df1d420cd715aea3400bfea9b87ed0f3bb47e","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e","shasum":""},"type":"library","time":"2013-05-24T19:19:25+00:00","autoload":{"psr-0":{"":"src\/"}},"extra":{"symfony-app-dir":"app","symfony-web-dir":"web","branch-alias":{"dev-master":"0.1-dev"}},"require":{"php":">=5.3.3","symfony\/symfony":"2.2.*","doctrine\/orm":">=2.2,<3.0,>=2.2.3","doctrine\/doctrine-bundle":"1.2.*","twig\/extensions":"1.0.*","symfony\/assetic-bundle":"2.1.*","symfony\/swiftmailer-bundle":"2.2.*","symfony\/monolog-bundle":"2.2.*","sensio\/distribution-bundle":"2.2.*","sensio\/framework-extra-bundle":"2.2.*","sensio\/generator-bundle":"2.2.*","jms\/security-extra-bundle":"1.4.*","jms\/di-extra-bundle":"1.3.*","knplabs\/packagist-api":"dev-master"},"require-dev":{"guzzle\/plugin-mock":"*"}}},"type":"library","repository":"https:\/\/github.com\/PUGX\/badge-poser","downloads":{"total":99,"monthly":12,"daily":9},"favers":9}}';
        $this->packagistClient = $this->createPackagistClient($data, 200);

        $image = '<svg xmlns="http://www.w3.org/2000/svg" width="79" height="18"><linearGradient id="a" x2="0" y2="100%"><stop offset="0" stop-color="#fff" stop-opacity=".7"/><stop offset=".1" stop-color="#aaa" stop-opacity=".1"/><stop offset=".9" stop-opacity=".3"/><stop offset="1" stop-opacity=".5"/></linearGradient><rect rx="4" width="79" height="18" fill="#555"/><rect rx="4" x="44" width="35" height="18" fill="#28a3df"/><path fill="#28a3df" d="M44 0h4v18h-4z"/><rect rx="4" width="79" height="18" fill="url(#a)"/><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11"><text x="23" y="13" fill="#010101" fill-opacity=".3">stable</text><text x="23" y="12">stable</text><text x="60.5" y="13" fill="#010101" fill-opacity=".3">v2.0</text><text x="60.5" y="12">v2.0</text></g></svg>';
        $this->imageHttpClient = $this->createHttpClient($image, 200);
    }

    public function testDownloadsAction()
    {
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->packagistClient);
        static::$kernel->getContainer()->set('image_http_client', $this->imageHttpClient);
        $client->request('GET', '/pugx/badge-poser/d/total');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testLatestStableAction()
    {
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->packagistClient);
        $client->request('GET', '/pugx/badge-poser/version');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testLatestUnstableAction()
    {
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->packagistClient);
        $client->request('GET', '/pugx/badge-poser/v/unstable');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $response = $client->getResponse();
        $this->assertRegExp('/s-maxage=3600/', $response->headers->get('Cache-Control'));
    }

    public function testLicenseAction()
    {
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->packagistClient);
        $client->request('GET', '/pugx/badge-poser/license');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testIfPackageDoesntExist()
    {
        $data = '{"status":"error","message":"Package not found"}';

        $packagistClient = $this->createPackagistClient($data, 500);

        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $packagistClient);
        static::$kernel->getContainer()->set('image_http_client', $this->imageHttpClient);
        $client->request('GET', '/pugx/microsoft-lover/d/total');
        $this->assertTrue($client->getResponse()->isServerError());
    }

    public function testSearchPackagist()
    {
        $data = '{"results":[{"name":"hpatoio\/deploy-bundle","description":"Brings Symfony 1.4 project:deploy command to Symfony2.","url":"https:\/\/packagist.org\/packages\/hpatoio\/deploy-bundle","downloads":1217,"favers":1},{"name":"hpatoio\/bitly-api","description":"PHP Library based on Guzzle to consume Bit.ly API","url":"https:\/\/packagist.org\/packages\/hpatoio\/bitly-api","downloads":5,"favers":1},{"name":"hpatoio\/bitly-bundle","description":"Integrate hpatoio\/bitly-api in your Symfony2 project","url":"https:\/\/packagist.org\/packages\/hpatoio\/bitly-bundle","downloads":2,"favers":1},{"name":"hpatoio\/commonbackend-bundle","description":"Backend goodies","url":"https:\/\/packagist.org\/packages\/hpatoio\/commonbackend-bundle","downloads":11,"favers":0}],"total":4}';
        $packagistClient = $this->createPackagistClient($data, 200);

        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $packagistClient);
        $client->request('GET', '/search_packagist?name=hpatoio');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    private function createHttpClient($data, $status = 200)
    {
        $packagistResponse = new \Guzzle\Http\Message\Response($status);
        $packagistResponse->setBody($data);
        $plugin = new \Guzzle\Plugin\Mock\MockPlugin();
        $plugin->addResponse($packagistResponse);
        $clientHttp = new \Guzzle\Http\Client();
        $clientHttp->addSubscriber($plugin);

        return $clientHttp;
    }

    private function createPackagistClient($data, $status = 200)
    {
        return new Client($this->createHttpClient($data, $status));
    }

}
