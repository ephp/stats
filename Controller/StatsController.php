<?php

namespace Ephp\StatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/stats")
 */
class StatsController extends Controller {
    
    use \Ephp\UtilityBundle\Controller\Traits\BaseController;

    /**
     * @Route("/last/{area}/{n}", name="stats_last", defaults={"n":10})
     * @Template()
     */
    public function lastAction($area, $n) {
        return array('visite' => $this->getRepository('EphpStatsBundle:Visita')->last($area, $n));
    }

}
