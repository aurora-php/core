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
 * @copyright   Copyright (c) 2016 by Harald Lapp
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
        E_ERROR             => \Psr\Log\LogLevel::ERROR,
        E_WARNING           => \Psr\Log\LogLevel::WARNING,
        E_PARSE             => \Psr\Log\LogLevel::ERROR,
        E_NOTICE            => \Psr\Log\LogLevel::NOTICE,
        E_CORE_ERROR        => \Psr\Log\LogLevel::ERROR,
        E_CORE_WARNING      => \Psr\Log\LogLevel::WARNING,
        E_COMPILE_ERROR     => \Psr\Log\LogLevel::ERROR,
        E_COMPILE_WARNING   => \Psr\Log\LogLevel::WARNING,
        E_USER_ERROR        => \Psr\Log\LogLevel::ERROR,
        E_USER_WARNING      => \Psr\Log\LogLevel::WARNING,
        E_USER_NOTICE       => \Psr\Log\LogLevel::NOTICE,
        E_STRICT            => \Psr\Log\LogLevel::ERROR,
        E_RECOVERABLE_ERROR => \Psr\Log\LogLevel::ERROR,
        E_DEPRECATED        => \Psr\Log\LogLevel::WARNING,
        E_USER_DEPRECATED   => \Psr\Log\LogLevel::WARNING
    );

    /**
     * Instance of a logger.
     *
     * @type    \Psr\Log\LoggerInterface
     */
    private static $logger = null;

    /**
     * Configure a logger instance to write error output to (instead of throwing an error exception by default).
     *
     * @param   \Psr\Log\LoggerInterface     $logger         Logger instance.
     */
    public static function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    /**
     * Error handler.
     */
    public static function errorHandler($code, $msg, $file, $line)
    {
        \Octris\Debug::getInstance()->error(
            $file,
            $line,
            [
                'code' => $code,
                'message' => $msg
            ],
            null,
            new \ErrorException($msg, $code, 0, $file, $line)
        );
    }
}

// register error handler
set_error_handler(function ($code, $msg, $file, $line) {
    \Octris\Core\Error::errorHandler($code, $msg, $file, $line);
}, E_ALL);
