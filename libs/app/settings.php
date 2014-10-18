<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app
    /**
     * user specific application settings handling
     *
     * @octdoc      c:core/settings
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald.lapp@gmail.com>
     */
    class settings
    /**/
    {
        /**
         * Stores instance of settings object.
         *
         * @octdoc  p:settings/$instance
         * @type    \org\octris\core\settings
         */
        protected $instance = null;
        /**/
        
        /**
         * Protected constructor to force using getInstance.
         *
         * @octdoc  m:settings/__construct
         */
        protected function __construct()
        /**/
        {
        }
        
        /**
         * Private clone method to prevent multiple instances of object.
         *
         * @octdoc  m:settings/__clone
         */
        private function __clone()
        /**/
        {
        }
        
        /**
         * Return instance of settings object.
         *
         * @octdoc  m:settings/getInstance
         */
        public function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }
            
            return self::$instance;
        }
    }
}
