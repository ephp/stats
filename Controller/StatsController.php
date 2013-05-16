<?php

namespace Ephp\StatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StatsController extends Controller
{
    public function indexAction()
    {
        return array('name' => 'pippo');
    }
}
