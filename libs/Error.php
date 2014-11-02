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
 * Error class.
 *
 * @copyright   Copyright (c) 2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Error
{
    /**
     * PHP error type to logger type mapping.
     *
     * @type    array
     */
    protected static $map = array(
        E_ERROR             => \Octris\Core\Logger::T_ERROR,
        E_WARNING           => \Octris\Core\Logger::T_WARNING,
        E_PARSE             => \Octris\Core\Logger::T_ERROR,
        E_NOTICE            => \Octris\Core\Logger::T_NOTICE,
        E_CORE_ERROR        => \Octris\Core\Logger::T_ERROR,
        E_CORE_WARNING      => \Octris\Core\Logger::T_WARNING,
        E_COMPILE_ERROR     => \Octris\Core\Logger::T_ERROR,
        E_COMPILE_WARNING   => \Octris\Core\Logger::T_WARNING,
        E_USER_ERROR        => \Octris\Core\Logger::T_ERROR,
        E_USER_WARNING      => \Octris\Core\Logger::T_WARNING,
        E_USER_NOTICE       => \Octris\Core\Logger::T_NOTICE,
        E_STRICT            => \Octris\Core\Logger::T_ERROR,
        E_RECOVERABLE_ERROR => \Octris\Core\Logger::T_ERROR,
        E_DEPRECATED        => \Octris\Core\Logger::T_WARNING,
        E_USER_DEPRECATED   => \Octris\Core\Logger::T_WARNING,
        E_ALL               => \Octris\Core\Logger::T_ALL
    );

    /**
     * Instance of a logger.
     *
     * @type    \Octris\Core\Logger
     */
    private static $logger = null;

    /**
     * Configure a logger instance to write error output to (instead of throwing an error exception by default).
     *
     * @param   \Octris\Core\Logger     $logger         Logger instance.
     */
    public static function setLogger(\Octris\Core\Logger $logger)
    {
        self::$logger = $logger;
    }

    /**
     * Error handler.
     */
    public static function errorHandler($code, $msg, $file, $line)
    {
        throw new \ErrorException($msg, $code, 0, $file, $line);
    }
}

// register error handler
set_error_handler(function ($code, $msg, $file, $line) {
    \Octris\Core\Error::errorHandler($code, $msg, $file, $line);
}, E_ALL);
