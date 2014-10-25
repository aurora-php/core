<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core {
    /**
     * Error class.
     * 
     * @octdoc      c:core/error
     * @copyright   Copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class error
    {
        /**
         * PHP error type to logger type mapping.
         *
         * @octdoc  p:error/$map
         * @type    array
         */
        protected static $map = array(
            E_ERROR             => \octris\core\logger::T_ERROR,
            E_WARNING           => \octris\core\logger::T_WARNING,
            E_PARSE             => \octris\core\logger::T_ERROR,
            E_NOTICE            => \octris\core\logger::T_NOTICE,
            E_CORE_ERROR        => \octris\core\logger::T_ERROR,
            E_CORE_WARNING      => \octris\core\logger::T_WARNING,
            E_COMPILE_ERROR     => \octris\core\logger::T_ERROR,
            E_COMPILE_WARNING   => \octris\core\logger::T_WARNING,
            E_USER_ERROR        => \octris\core\logger::T_ERROR,
            E_USER_WARNING      => \octris\core\logger::T_WARNING,
            E_USER_NOTICE       => \octris\core\logger::T_NOTICE,
            E_STRICT            => \octris\core\logger::T_ERROR,
            E_RECOVERABLE_ERROR => \octris\core\logger::T_ERROR,
            E_DEPRECATED        => \octris\core\logger::T_WARNING,
            E_USER_DEPRECATED   => \octris\core\logger::T_WARNING,
            E_ALL               => \octris\core\logger::T_ALL
        );
        /**/
        
        /**
         * Instance of a logger.
         *
         * @octdoc  p:error/$logger
         * @type    \octris\core\logger
         */
        private static $logger = null;
        /**/

        /**
         * Configure a logger instance to write error output to (instead of throwing an error exception by default).
         *
         * @octdoc  m:error/setLogger
         * @param   \octris\core\logger     $logger         Logger instance.
         */
        public static function setLogger(\octris\core\logger $logger)
        {
            self::$logger = $logger;
        }
        
        /**
         * Error handler.
         *
         * @octdoc  m:error/errorHandler
         */
        public static function errorHandler($code, $msg, $file, $line)
        {
            throw new \ErrorException($msg, $code, 0, $file, $line);        
        }
    }

    // register error handler
    set_error_handler(function($code, $msg, $file, $line) {
        \octris\core\error::errorHandler($code, $msg, $file, $line);
    }, E_ALL);
}
