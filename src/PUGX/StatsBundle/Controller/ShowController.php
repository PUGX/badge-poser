<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\StatsBundle\Controller;

use PUGX\StatsBundle\Service\ReaderInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class PageController
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class ShowController extends ContainerAware
{
    const STATS_STARTED = '2013-11-01 00:00:00';
    const GITHUB_PROXY_STARTED = '2014-01-00 00:00:00';

    /**
     * @Route("/stats/monthly",
     * name = "pugx_stat_monthly"
     * )
     *
     * @Method({"GET"})
     * @Template
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @return Response
     */
    public function monthlyRenderAction()
    {
        $from = new \Datetime(self::STATS_STARTED);
        $to = new \Datetime("now");
        $githubSSLProxyDate = new \Datetime(self::GITHUB_PROXY_STARTED);

        $arrayOfData = $this->container->get('stats_reader')->totalDataOfAccessesByInterval($from, $to, ReaderInterface::MONTH);

        $data = '0, ';
        $labels = '"October `13", ';

        foreach ($arrayOfData as $element) {
            $data .=  $element->getValue().', '; ;

            if ($element->getDatetime() >= $githubSSLProxyDate) {
                $labels .=  '"'.$element->getDatetime()->format("F `y").' *",';
            } else {
                $labels .=  '"'.$element->getDatetime()->format("F `y").'",';
            }
        }
        $data = rtrim($data, ',');
        $labels = rtrim($labels, ',');


        return array(
            'labels' => $labels,
            'data' => $data
        );
    }
}
