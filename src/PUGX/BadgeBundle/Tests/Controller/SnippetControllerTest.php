<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests\Controller;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Packagist\Api\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SnippetControllerTest extends WebTestCase
{
    // this setUp fake the request/response, if you comment this function the test'd run only with internet connection
    public function setUp()
    {
        // see the config_test.yml, there's a parameters in order to not stream the output using FakeImageCreator.
        $data = '{"package":{"name":"pugx\/badge-poser","description":"add badges on your readme, such as downloads number or latest version.","time":"2013-05-24T14:45:06+00:00","maintainers":[{"name":"liuggio","email":"liuggio@gmail.com"}],"versions":{"dev-master":{"name":"pugx\/badge-poser","description":"add badges on your readme, such as downloads number or latest version.","keywords":[],"homepage":"","version":"dev-master","version_normalized":"9999999-dev","license":[],"authors":[],"source":{"type":"git","url":"https:\/\/github.com\/PUGX\/badge-poser.git","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e"},"dist":{"type":"zip","url":"https:\/\/api.github.com\/repos\/PUGX\/badge-poser\/zipball\/024df1d420cd715aea3400bfea9b87ed0f3bb47e","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e","shasum":""},"type":"library","time":"2013-05-24T19:19:25+00:00","autoload":{"psr-0":{"":"src\/"}},"extra":{"symfony-app-dir":"app","symfony-web-dir":"web","branch-alias":{"dev-master":"0.1-dev"}},"require":{"php":">=5.3.3","symfony\/symfony":"2.2.*","doctrine\/orm":">=2.2,<3.0,>=2.2.3","doctrine\/doctrine-bundle":"1.2.*","twig\/extensions":"1.0.*","symfony\/assetic-bundle":"2.1.*","symfony\/swiftmailer-bundle":"2.2.*","symfony\/monolog-bundle":"2.2.*","sensio\/distribution-bundle":"2.2.*","sensio\/framework-extra-bundle":"2.2.*","sensio\/generator-bundle":"2.2.*","jms\/security-extra-bundle":"1.4.*","jms\/di-extra-bundle":"1.3.*","knplabs\/packagist-api":"dev-master"},"require-dev":{"guzzle\/plugin-mock":"*"}},"dev-develop":{"name":"pugx\/badge-poser","description":"add badges on your readme, such as downloads number or latest version.","keywords":[],"homepage":"","version":"dev-develop","version_normalized":"dev-develop","license":[],"authors":[],"source":{"type":"git","url":"https:\/\/github.com\/PUGX\/badge-poser.git","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e"},"dist":{"type":"zip","url":"https:\/\/api.github.com\/repos\/PUGX\/badge-poser\/zipball\/024df1d420cd715aea3400bfea9b87ed0f3bb47e","reference":"024df1d420cd715aea3400bfea9b87ed0f3bb47e","shasum":""},"type":"library","time":"2013-05-24T19:19:25+00:00","autoload":{"psr-0":{"":"src\/"}},"extra":{"symfony-app-dir":"app","symfony-web-dir":"web","branch-alias":{"dev-master":"0.1-dev"}},"require":{"php":">=5.3.3","symfony\/symfony":"2.2.*","doctrine\/orm":">=2.2,<3.0,>=2.2.3","doctrine\/doctrine-bundle":"1.2.*","twig\/extensions":"1.0.*","symfony\/assetic-bundle":"2.1.*","symfony\/swiftmailer-bundle":"2.2.*","symfony\/monolog-bundle":"2.2.*","sensio\/distribution-bundle":"2.2.*","sensio\/framework-extra-bundle":"2.2.*","sensio\/generator-bundle":"2.2.*","jms\/security-extra-bundle":"1.4.*","jms\/di-extra-bundle":"1.3.*","knplabs\/packagist-api":"dev-master"},"require-dev":{"guzzle\/plugin-mock":"*"}}},"type":"library","repository":"https:\/\/github.com\/PUGX\/badge-poser","downloads":{"total":99,"monthly":12,"daily":9},"favers":9}}';
        $this->packagistClient = new Client(new \GuzzleHttp\Client([
            'handler' => HandlerStack::create(new MockHandler([
                new Response(200, [], $data),
            ])),
        ]));
    }

    public function testAllAction()
    {
        $expectedData = '{"clip_all":{"markdown":"[![Latest Stable Version](http:\/\/localhost\/pugx\/badge-poser\/v\/stable)](https:\/\/packagist.org\/packages\/pugx\/badge-poser) [![Total Downloads](http:\/\/localhost\/pugx\/badge-poser\/downloads)](https:\/\/packagist.org\/packages\/pugx\/badge-poser) [![Latest Unstable Version](http:\/\/localhost\/pugx\/badge-poser\/v\/unstable)](https:\/\/packagist.org\/packages\/pugx\/badge-poser) [![License](http:\/\/localhost\/pugx\/badge-poser\/license)](https:\/\/packagist.org\/packages\/pugx\/badge-poser)"},"latest_stable_version":{"markdown":"[![Latest Stable Version](http:\/\/localhost\/pugx\/badge-poser\/v\/stable)](https:\/\/packagist.org\/packages\/pugx\/badge-poser)","img":"http:\/\/localhost\/pugx\/badge-poser\/v\/stable"},"total":{"markdown":"[![Total Downloads](http:\/\/localhost\/pugx\/badge-poser\/downloads)](https:\/\/packagist.org\/packages\/pugx\/badge-poser)","img":"http:\/\/localhost\/pugx\/badge-poser\/downloads"},"latest_unstable_version":{"markdown":"[![Latest Unstable Version](http:\/\/localhost\/pugx\/badge-poser\/v\/unstable)](https:\/\/packagist.org\/packages\/pugx\/badge-poser)","img":"http:\/\/localhost\/pugx\/badge-poser\/v\/unstable"},"license":{"markdown":"[![License](http:\/\/localhost\/pugx\/badge-poser\/license)](https:\/\/packagist.org\/packages\/pugx\/badge-poser)","img":"http:\/\/localhost\/pugx\/badge-poser\/license"},"monthly":{"markdown":"[![Monthly Downloads](http:\/\/localhost\/pugx\/badge-poser\/d\/monthly)](https:\/\/packagist.org\/packages\/pugx\/badge-poser)","img":"http:\/\/localhost\/pugx\/badge-poser\/d\/monthly"},"daily":{"markdown":"[![Daily Downloads](http:\/\/localhost\/pugx\/badge-poser\/d\/daily)](https:\/\/packagist.org\/packages\/pugx\/badge-poser)","img":"http:\/\/localhost\/pugx\/badge-poser\/d\/daily"},"version":{"markdown":"[![Version](http:\/\/localhost\/pugx\/badge-poser\/version)](https:\/\/packagist.org\/packages\/pugx\/badge-poser)","img":"http:\/\/localhost\/pugx\/badge-poser\/version"},"composerlock":{"markdown":"[![composer.lock](http:\/\/localhost\/pugx\/badge-poser\/composerlock)](https:\/\/packagist.org\/packages\/pugx\/badge-poser)","img":"http:\/\/localhost\/pugx\/badge-poser\/composerlock"},"gitattributes":{"markdown":"[![.gitattributes](http:\/\/localhost\/pugx\/badge-poser\/gitattributes)](https:\/\/packagist.org\/packages\/pugx\/badge-poser)","img":"http:\/\/localhost\/pugx\/badge-poser\/gitattributes"},"repository":{"html":"pugx\/badge-poser"}}';

        $client = static::createClient();
        static::$kernel->getContainer()->set('packagist_client', $this->packagistClient);
        $client->request('GET','/snippet/all/?repository=pugx/badge-poser');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($expectedData, $client->getResponse()->getContent());
    }

}
