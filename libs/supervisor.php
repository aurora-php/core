<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * Super visor for handling asynchronous (non-blocking) tasks.
     *
     * @octdoc      c:core/supervisor
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class supervisor
    /**/
    {
        /**
         * Services.
         *
         * @octdoc  p:supervisor/$services
         * @type    array
         */
        protected $services = array();
        /**/

        /**
         * Whether to autostart polling.
         *
         * @octdoc  p:supervisor/$autostart
         * @type    bool
         */
        protected $autostart = false;
        /**/

        /**
         * Whether supervisor is already running.
         *
         * @octdoc  p:supervisor/$running
         * @type    bool
         */
        protected $running = false;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:supervisor/__construct
         * @param   bool                                    $autostart          Whether to start requests as soon as adding the first service.
         */
        public function __construct($autostart = true)
        /**/
        {
            $this->autostart = $autostart;
        }

        /**
         * Start polling.
         *
         * @octdoc  m:supervisor/start
         */
        public function start()
        /**/
        {
            if (!$this->running) {
                $this->running = true;
            }
        }

        /**
         * Poll services.
         *
         * @octdoc  m:supervisor/poll
         */
        public function poll()
        /**/
        {
            if ($this->running) {
                ...
            }
        }

        /**
         * Register a service for example curl, mysql, etc.
         *
         * @octdoc m:supervisor/addService
         * @param  \org\octris\core\supervisor\service_if   $service            An instance of a service class.
         */
        public function addService(\org\octris\core\supervisor\service_if $service)
        /**/
        {
            $this->services[] = $service;
        }
    }
}
