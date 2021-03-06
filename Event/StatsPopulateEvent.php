<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ephp\StatsBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Ephp\StatsBundle\Service\Generator;

/**
 * Manage populate event 
 * 
 * @author depely
 */
class StatsPopulateEvent extends Event
{
    const onSitemapPopulate = 'ephp_stats.populate';

    protected $generator;

    /**
     * Allows creating EventListeners for particular sitemap sections, used when dumping
     *
     * @var string
     */
    protected $section;

    public function __construct(Generator $generator, $section = null)
    {
        $this->generator = $generator;
        $this->section = $section;
    }

    /**
     * @return Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Section to be processed, null means any
     *
     * @return null|string
     */
    public function getSection()
    {
        return $this->section;
    }
}