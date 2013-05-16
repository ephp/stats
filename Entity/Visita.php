<?php

namespace Ephp\StatsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Visita
 *
 * @ORM\Table(name="stats_visite", indexes={
 *            @ORM\index(name="route_idx", columns={"route"}),
 *            @ORM\index(name="uid_idx", columns={"uid"}),
 *            @ORM\index(name="ip_idx", columns={"ip"}),
 *            @ORM\index(name="open_at_idx", columns={"open_at"})
 * })
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Ephp\StatsBundle\Entity\VisitaRepository")
 */
class Visita {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=64)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="session", type="string", length=255)
     */
    private $session;

    /**
     * @var string
     *
     * @ORM\Column(name="route_params", type="array")
     */
    private $route_params;

    /**
     * @var string
     *
     * @ORM\Column(name="uid", type="string", length=32)
     */
    private $uid;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15, nullable=true)
     */
    private $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="open_at", type="datetime")
     */
    private $open_at;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="string", length=255, nullable=true)
     */
    private $user_agent;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=5, nullable=true)
     */
    private $locale;

    /**
     * Owning Side
     *
     * @ORM\ManyToMany(targetEntity="Area")
     * @ORM\JoinTable(name="stats_visite_aree",
     *      joinColumns={@ORM\JoinColumn(name="visita_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="area_id", referencedColumnName="id")}
     *      )
     */
    private $aree;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Visita
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Visita
     */
    public function setIp($ip) {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * Set open_at
     *
     * @param \DateTime $openAt
     * @return Visita
     */
    public function setOpenAt($openAt) {
        $this->open_at = $openAt;

        return $this;
    }

    /**
     * Get open_at
     *
     * @return \DateTime 
     */
    public function getOpenAt() {
        return $this->open_at;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return Visita
     */
    public function setLocale($locale) {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string 
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return Visita
     */
    public function setRoute($route) {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Set route_params
     *
     * @param array $routeParams
     * @return Visita
     */
    public function setRouteParams($routeParams) {
        $this->route_params = $routeParams;

        return $this;
    }

    /**
     * Get route_params
     *
     * @return array 
     */
    public function getRouteParams() {
        return $this->route_params;
    }

    /**
     * Set uid
     *
     * @param string $uid
     * @return Visita
     */
    public function setUid($uid) {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return string 
     */
    public function getUid() {
        return $this->uid;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->url = str_replace('/app_dev.php', '', $this->url);
        $this->open_at = new \DateTime();
        $this->uid = md5($this->url);
    }

    /**
     * Set session
     *
     * @param string $session
     * @return Visita
     */
    public function setSession($session) {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return string 
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * Set user_agent
     *
     * @param string $userAgent
     * @return Visita
     */
    public function setUserAgent($userAgent) {
        $this->user_agent = $userAgent;

        return $this;
    }

    /**
     * Get user_agent
     *
     * @return string 
     */
    public function getUserAgent() {
        return $this->user_agent;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aree = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add aree
     *
     * @param \Ephp\StatsBundle\Entity\Area $aree
     * @return Visita
     */
    public function addAree(\Ephp\StatsBundle\Entity\Area $aree)
    {
        $this->aree[] = $aree;
    
        return $this;
    }

    /**
     * Remove aree
     *
     * @param \Ephp\StatsBundle\Entity\Area $aree
     */
    public function removeAree(\Ephp\StatsBundle\Entity\Area $aree)
    {
        $this->aree->removeElement($aree);
    }

    /**
     * Get aree
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAree()
    {
        return $this->aree;
    }
}
