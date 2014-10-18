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
     * Implementation of a central registry uses DI container as storage
     *
     * @octdoc      c:core/registry
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald.lapp@gmail.com>
     */
    class registry extends \org\octris\core\type\container
    /**/
    {
        /**
         * Stores instance of registry object.
         *
         * @octdoc  p:registry/$instance
         * @type    \org\octris\core\registry
         */
        private static $instance = null;
        /**/

        /**
         * Constructor is protected to prevent instanciating registry.
         *
         * @octdoc  m:registry/__construct
         */
        protected function __construct()
        /**/
        {
        }

        /**
         * Clone is private to prevent multipleinstances of registry.
         *
         * @octdoc  m:registry/__clone
         */
        private function __clone()
        /**/
        {
        }

        /**
         * Return instance of registry.
         *
         * @octdoc  m:registry/getInstance
         * @return  \org\octris\core\registry           instance of registry
         */
        public static function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }

            return self::$instance;
        }
    }
}
