<?php

namespace PUGX\BadgeBundle\Tests\Controller;

use Packagist\Api\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BadgeControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $data ='{"package":{"name":"sylius\/sylius","description":"Modern ecommerce for Symfony2","time":"2013-01-18T20:48:01+00:00","maintainers":[{"name":"pjedrzejewski","email":"pjedrzejewski@diweb.pl"}],"versions":{"dev-master":{"name":"sylius\/sylius","description":"Modern ecommerce for Symfony2","keywords":[],"homepage":"http:\/\/sylius.org","version":"dev-master","version_normalized":"9999999-dev","license":["MIT"],"authors":[{"name":"Pawe\u0142 J\u0119drzejewski","email":"pjedrzejewski@diweb.pl","homepage":"http:\/\/pjedrzejewski.com"},{"name":"Sylius project","homepage":"http:\/\/sylius.org"},{"name":"Community contributions","homepage":"http:\/\/github.com\/Sylius\/Sylius\/contributors"}],"source":{"type":"git","url":"https:\/\/github.com\/Sylius\/Sylius.git","reference":"1f0e49a6d4e9b6ab3a84cbd962f772c707461640"},"dist":{"type":"zip","url":"https:\/\/api.github.com\/repos\/Sylius\/Sylius\/zipball\/1f0e49a6d4e9b6ab3a84cbd962f772c707461640","reference":"1f0e49a6d4e9b6ab3a84cbd962f772c707461640","shasum":""},"type":"library","time":"2013-03-17T12:27:32+00:00","autoload":{"psr-0":{"Context":"features\/"}},"extra":{"symfony-app-dir":"sylius","symfony-web-dir":"web"},"require":{"php":">=5.3.3","doctrine\/orm":">=2.2.3,<2.4-dev","doctrine\/doctrine-fixtures-bundle":"*","twig\/extensions":"1.0.*","symfony\/assetic-bundle":"2.1.*","sylius\/core-bundle":"0.1.*","sylius\/web-bundle":"0.1.*","symfony\/symfony":">=2.2,<2.3-dev","doctrine\/doctrine-bundle":"1.2.*","symfony\/swiftmailer-bundle":"2.2.*","symfony\/monolog-bundle":"2.2.*","sensio\/distribution-bundle":"2.2.*","mathiasverraes\/money":"dev-master@dev"},"require-dev":{"behat\/behat":"2.4.*","behat\/symfony2-extension":"*","behat\/mink-extension":"*","behat\/mink-browserkit-driver":"*","phpspec\/phpspec2":"dev-develop","behat\/mink-selenium2-driver":"*"}},"dev-checkout":{"name":"sylius\/sylius","description":"Modern ecommerce for Symfony2","keywords":[],"homepage":"","version":"dev-checkout","version_normalized":"dev-checkout","license":["MIT"],"authors":[{"name":"Pawe\u0142 J\u0119drzejewski","email":"pjedrzejewski@diweb.pl","homepage":"http:\/\/pjedrzejewski.com"},{"name":"Sylius project","homepage":"http:\/\/sylius.org"},{"name":"Community contributions","homepage":"http:\/\/github.com\/Sylius\/Sylius\/contributors"}],"source":{"type":"git","url":"https:\/\/github.com\/Sylius\/Sylius.git","reference":"cb0a489db41707d5df078f1f35e028e04ffd9e8e"},"dist":{"type":"zip","url":"https:\/\/api.github.com\/repos\/Sylius\/Sylius\/zipball\/cb0a489db41707d5df078f1f35e028e04ffd9e8e","reference":"cb0a489db41707d5df078f1f35e028e04ffd9e8e","shasum":""},"type":"library","time":"2013-03-01T22:22:37+00:00","autoload":{"psr-0":{"Context":"features\/"}},"extra":{"symfony-app-dir":"sylius","symfony-web-dir":"web"},"require":{"php":">=5.3.3","symfony\/symfony":">=2.2,<2.3-dev","doctrine\/orm":">=2.2.3,<2.4-dev","doctrine\/doctrine-bundle":"1.2.*","doctrine\/doctrine-fixtures-bundle":"*","twig\/extensions":"1.0.*","symfony\/assetic-bundle":"2.1.*","symfony\/swiftmailer-bundle":"2.2.*","symfony\/monolog-bundle":"2.2.*","sensio\/distribution-bundle":"2.2.*","sylius\/core-bundle":"0.1.*","sylius\/web-bundle":"0.1.*","mathiasverraes\/money":"dev-master@dev"},"require-dev":{"behat\/behat":"2.4.*","behat\/symfony2-extension":"*","behat\/mink-extension":"*","behat\/mink-browserkit-driver":"*","phpspec\/phpspec2":"dev-develop","behat\/mink-selenium2-driver":"*"}}},"type":"library","repository":"https:\/\/github.com\/Sylius\/Sylius.git","downloads":{"total":41,"monthly":30,"daily":0},"favers":0}}';
        $packagistResponse = new \Guzzle\Http\Message\Response(200);
        $packagistResponse->setBody($data);
        $plugin = new \Guzzle\Plugin\Mock\MockPlugin();
        $plugin->addResponse($packagistResponse);
        $clientHttp = new \Guzzle\Http\Client();
        $clientHttp->addSubscriber($plugin);

        $packagistClient = new Client($clientHttp);
        //We override the regular service
        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist.client', $packagistClient);

        $crawler = $client->request('GET', '/pugx/badge-poser/downloads/total.png');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}