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
     * Error class.
     * 
     * @octdoc      c:core/error
     * @copyright   Copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class error
    /**/
    {
        /**
         * PHP error type to logger type mapping.
         *
         * @octdoc  p:error/$map
         * @type    array
         */
        protected static $map = array(
            E_ERROR             => \org\octris\core\logger::T_ERROR,
            E_WARNING           => \org\octris\core\logger::T_WARNING,
            E_PARSE             => \org\octris\core\logger::T_ERROR,
            E_NOTICE            => \org\octris\core\logger::T_NOTICE,
            E_CORE_ERROR        => \org\octris\core\logger::T_ERROR,
            E_CORE_WARNING      => \org\octris\core\logger::T_WARNING,
            E_COMPILE_ERROR     => \org\octris\core\logger::T_ERROR,
            E_COMPILE_WARNING   => \org\octris\core\logger::T_WARNING,
            E_USER_ERROR        => \org\octris\core\logger::T_ERROR,
            E_USER_WARNING      => \org\octris\core\logger::T_WARNING,
            E_USER_NOTICE       => \org\octris\core\logger::T_NOTICE,
            E_STRICT            => \org\octris\core\logger::T_ERROR,
            E_RECOVERABLE_ERROR => \org\octris\core\logger::T_ERROR,
            E_DEPRECATED        => \org\octris\core\logger::T_WARNING,
            E_USER_DEPRECATED   => \org\octris\core\logger::T_WARNING,
            E_ALL               => \org\octris\core\logger::T_ALL
        );
        /**/
        
        /**
         * Instance of a logger.
         *
         * @octdoc  p:error/$logger
         * @type    \org\octris\core\logger
         */
        private static $logger = null;
        /**/

        /**
         * Configure a logger instance to write error output to (instead of throwing an error exception by default).
         *
         * @octdoc  m:error/setLogger
         * @param   \org\octris\core\logger     $logger         Logger instance.
         */
        public static function setLogger(\org\octris\core\logger $logger)
        /**/
        {
            self::$logger = $logger;
        }
        
        /**
         * Error handler.
         *
         * @octdoc  m:error/errorHandler
         */
        public static function errorHandler($code, $msg, $file, $line)
        /**/
        {
            throw new \ErrorException($msg, $code, 0, $file, $line);        
        }
    }

    // register error handler
    set_error_handler(function($code, $msg, $file, $line) {
        \org\octris\core\error::errorHandler($code, $msg, $file, $line);
    }, E_ALL);
}
