<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core;

/**
 * Super visor for handling asynchronous (non-blocking) tasks.
 *
 * @copyright   copyright (c) 2013 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Supervisor
{
    /**
     * Services.
     *
     * @type    array
     */
    protected $services = array();
    
    /**
     * Whether to autostart polling.
     *
     * @type    bool
     */
    protected $autostart = false;
    
    /**
     * Whether supervisor is already running.
     *
     * @type    bool
     */
    protected $running = false;
    
    /**
     * Constructor.
     *
     * @param   bool                                    $autostart          Whether to start requests as soon as adding the first service.
     */
    public function __construct($autostart = true)
    {
        $this->autostart = $autostart;
    }

    /**
     * Start polling.
     *
     */
    public function start()
    {
        if (!$this->running) {
            $this->running = true;
        }
    }

    /**
     * Poll services.
     *
     */
    public function poll()
    {
        if ($this->running) {
            // ...
        }
    }

    /**
     * Register a service for example curl, mysql, etc.
     *
     * @param  \Octris\Core\Supervisor\IService   $service            An instance of a service class.
     */
    public function addService(\Octris\Core\Supervisor\IService $service)
    {
        $this->services[] = $service;
    }
}
