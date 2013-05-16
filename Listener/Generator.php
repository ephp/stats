<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ephp\StatsBundle\Listener;

use Ephp\StatsBundle\Event\StatsPopulateEvent;
use Ephp\StatsBundle\Sitemap;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Ephp\StatsBundle\Sitemap\Sitemapindex;
use Ephp\StatsBundle\Sitemap\Url\Url;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Generator Manager service
 * 
 * @author David Epely <depely@prestaconcept.net>
 * @author Christophe Dolivet
 */
class Generator {

    /**
     *
     * @var Router
     */
    private $router;

    /**
     *
     * @var \appDevDebugProjectContainer 
     */
    private $container;

    /**
     *
     * @var EntityManager
     */
    private $em = null;

    /**
     *
     * @var GetResponseEvent 
     */
    private $event = null;

    /**
     *
     * @var Request 
     */
    private $request = null;

    public function __construct($router, $container, $em) {
        $this->router = $router;
        $this->container = $container;
        $this->em = $em;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $this->event = $event;
        $this->request = $event->getRequest();

        $rc = $this->router->getRouteCollection();
        /* @var $rc \Symfony\Component\Routing\RouteCollection */

        $route = $rc->get($this->request->get('_route'));
        if(!$route) return false;
        $stats = $route->getOption('stats');
        // Verifico che sia stata richiesta la memorizzazione delle statistiche
        if ($stats && is_array($stats)) {
            // Opzioni default in caso di assenza
            $options = array_merge(array(
                'area' => array('default'),
                    ), $stats);
            // trasformo area in un array
            if (!is_array($options['area'])) {
                $options['area'] = array($options['area']);
            }
            // Verifico che non siano stati richieste aree "dinamiche" dipendenti da un parametro della route
            $route = $this->request->get('_route');
            $route_params = $this->request->get('_route_params');
            if (isset($options['area_from'])) {
                // L'area dinamica deve essere in un array, potrebbe avere anche
                // i parametri from e chars necessari a prendere una sotto stringa 
                // del parametro richiesto
                if (!is_array($options['area_from'])) {
                    $options['area_from'] = array(
                        'param' => $options['area_from'],
                    );
                }
                // Se il parametro esiste, vado avanti e configuro la nuova area
                if (isset($route_params[$options['area_from']['param']])) {
                    $area = $route_params[$options['area_from']['param']];
                    if (isset($options['area_from']['from']) && isset($options['area_from']['chars'])) {
                        $area = substr($area, $options['area_from']['from'], $options['area_from']['chars']);
                    }
                    // Se c'è solo l'area default l'unica area che verrà memorizzata 
                    // sarà quella dinamica, altrimenti aggiungo all'array delle aree
                    if (count($options['area']) == 1 && $options['area'][0] == 'default') {
                        $options['area'][0] = $area;
                    } else {
                        $options['area'][] = $area;
                    }
                }
            }
            if (isset($options['entity'])) {
                // Controllo e parametrizzo l'entity
                if (!is_array($options['entity'])) {
                    $options['entity'] = array(
                        'entity' => $options['entity'],
                        'prefix' => $options['entity'],
                        'column' => 'id',
                        'param' => 'id',
                        'output' => 'id',
                    );
                } else {
                    $options['entity'] = array_merge(array(
                        'entity' => $options['entity']['entity'],
                        'prefix' => $options['entity']['entity'],
                        'column' => 'id',
                        'param' => 'id',
                        'output' => 'id',
                    ), $options['entity']);
                }
                // Se il parametro esiste, vado avanti e configuro la nuova area
                if (isset($route_params[$options['entity']['param']])) {
                    $param = $route_params[$options['entity']['param']];
                    $_entity = $this->em->getRepository($options['entity']['entity']);
                    $entity = $_entity->findOneBy(array($options['entity']['column'] => $param));
                    $fx = \Doctrine\Common\Util\Inflector::camelize("get_".$options['entity']['output']);
                    $area = $options['entity']['prefix'].'-'.$entity->$fx();
                    // Se c'è solo l'area default l'unica area che verrà memorizzata 
                    // sarà quella dinamica, altrimenti aggiungo all'array delle aree
                    if (count($options['area']) == 1 && $options['area'][0] == 'default') {
                        $options['area'][0] = $area;
                    } else {
                        $options['area'][] = $area;
                    }
                }
            }
            try {
                $this->em->beginTransaction();
                $visita = new \Ephp\StatsBundle\Entity\Visita();
                $visita->setUrl($this->router->generate($route, $route_params));
                $visita->setRoute($route);
                $visita->setRouteParams($route_params);
                $visita->setSession($this->request->getSession()->getId());
                $visita->setIp($this->request->getClientIp());
                $visita->setUserAgent($this->request->server->get('HTTP_USER_AGENT'));
                $locale = $this->request->getLanguages();
                $visita->setLocale(array_shift($locale));
                foreach ($options['area'] as $_area) {
                    $area = $this->em->getRepository('EphpStatsBundle:Area')->findOneBy(array('area' => $_area));
                    if(!$area) {
                        $area = new \Ephp\StatsBundle\Entity\Area();
                        $area->setArea($_area);
                        $this->em->persist($area);
                        $this->em->flush();
                    }
                    $visita->addAree($area);
                }
                $this->em->persist($visita);
                $this->em->flush();
                $this->em->commit();
            } catch (\Exception $e) {
                \Ephp\UtilityBundle\Utility\Debug::vd($e);
                $this->em->rollback();
                throw $e;
            }
        }
    }

}
