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
 * Base class of logging framework.
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Logger
{
    /**
     * Log levels.
     */
    const T_EMERGENCY = 1;
    const T_ALERT     = 2;
    const T_CRITICAL  = 4;
    const T_ERROR     = 8;
    const T_WARNING   = 16;
    const T_NOTICE    = 32;
    const T_INFO      = 64;
    const T_DEBUG     = 128;

    /**
     * Helper constants for making configuring writers more easy.
     *
     * * T_ALL -- logs every log level
     * * T_PRODUCTION -- logs T_EMERGENCY, T_ALERT, T_CRITICAL, T_ERROR
     */
    const T_ALL        = 255;
    const T_PRODUCTION = 15;

    /**
     * Configured writers.
     *
     * @type    array
     */
    private $writers = array(
        self::T_EMERGENCY => array(),
        self::T_ALERT     => array(),
        self::T_CRITICAL  => array(),
        self::T_ERROR     => array(),
        self::T_WARNING   => array(),
        self::T_NOTICE    => array(),
        self::T_INFO      => array(),
        self::T_DEBUG     => array()
    );

    /**
     * Logger instance.
     *
     * @type    \octris\core\logger
     */
    private static $instance = null;

    /**
     * Facility the error was logged from. Either a value set using setValue
     * will be used or an optional string provided for 'log' method.
     *
     * @type    string
     */
    protected $facility = '';

    /**
     * Standard data to write to log.
     *
     * @type    array
     */
    protected $data = array();

    /**
     * Constructor.
     */
    private function __construct()
    {
    }

    /**
     * Implements singleton pattern, returns instance of logger.
     *
     * @return  \octris\core\logger                     Logger instance.
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Set default value to always send to logger (expect it's overwritten)
     * in 'log' method. Note, that the special property 'facility' will be
     * used individually, see 'log' method.
     *
     * @param   string                          $name       Name of value to set.
     * @param   mixed                           $value      Value to set.
     */
    public function setDefault($name, $value)
    {
        if ($name == 'facility') {
            $this->facility = $value;
        } else {
            $this->data[$name] = $value;
        }
    }

    /**
     * Add log writer instance.
     *
     * @param   int                                 $level      Log level the logger belongs to.
     * @param   \Octris\Core\Logger\IWriter   $writer     Instance of logger to add.
     */
    public function addWriter($level, \Octris\Core\Logger\IWriter $writer)
    {
        foreach ($this->writers as $l => &$a) {
            if (($level & $l) === $l) {
                $a[] = $writer;
            }
        }
    }

    /**
     * Shortcut for log + T_EMERGENCY call.
     *
     * @param   string|\Exception   $notification       Either short message or exception to log.
     * @param   array               $data               Optional additional fields to set. See also 'setValue' method.
     * @param   string              $facility           Optional facility name eg. application name. See also 'setValue' method.
     */
    public static function emergency($notification, $data = array(), $facility = '')
    {
        static::getInstance()->log(self::T_EMERGENCY, $notification, $data, $facility);
    }

    /**
     * Shortcut for log + T_ALERT call.
     *
     * @param   string|\Exception   $notification       Either short message or exception to log.
     * @param   array               $data               Optional additional fields to set. See also 'setValue' method.
     * @param   string              $facility           Optional facility name eg. application name. See also 'setValue' method.
     */
    public static function alert($notification, $data = array(), $facility = '')
    {
        static::getInstance()->log(self::T_ALERT, $notification, $data, $facility);
    }

    /**
     * Shortcut for log + T_CRITICAL call.
     *
     * @param   string|\Exception   $notification       Either short message or exception to log.
     * @param   array               $data               Optional additional fields to set. See also 'setValue' method.
     * @param   string              $facility           Optional facility name eg. application name. See also 'setValue' method.
     */
    public static function critical($notification, $data = array(), $facility = '')
    {
        static::getInstance()->log(self::T_CRITICAL, $notification, $data, $facility);
    }

    /**
     * Shortcut for log + T_ERROR call.
     *
     * @param   string|\Exception   $notification       Either short message or exception to log.
     * @param   array               $data               Optional additional fields to set. See also 'setValue' method.
     * @param   string              $facility           Optional facility name eg. application name. See also 'setValue' method.
     */
    public static function error($notification, $data = array(), $facility = '')
    {
        static::getInstance()->log(self::T_ERROR, $notification, $data, $facility);
    }

    /**
     * Shortcut for log + T_WARNING call.
     *
     * @param   string|\Exception   $notification       Either short message or exception to log.
     * @param   array               $data               Optional additional fields to set. See also 'setValue' method.
     * @param   string              $facility           Optional facility name eg. application name. See also 'setValue' method.
     */
    public static function warning($notification, $data = array(), $facility = '')
    {
        static::getInstance()->log(self::T_WARNING, $notification, $data, $facility);
    }

    /**
     * Shortcut for log + T_NOTICE call.
     *
     * @param   string|\Exception   $notification       Either short message or exception to log.
     * @param   array               $data               Optional additional fields to set. See also 'setValue' method.
     * @param   string              $facility           Optional facility name eg. application name. See also 'setValue' method.
     */
    public static function notice($notification, $data = array(), $facility = '')
    {
        static::getInstance()->log(self::T_NOTICE, $notification, $data, $facility);
    }

    /**
     * Shortcut for log + T_INFO call.
     *
     * @param   string|\Exception   $notification       Either short message or exception to log.
     * @param   array               $data               Optional additional fields to set. See also 'setValue' method.
     * @param   string              $facility           Optional facility name eg. application name. See also 'setValue' method.
     */
    public static function info($notification, $data = array(), $facility = '')
    {
        static::getInstance()->log(self::T_INFO, $notification, $data, $facility);
    }

    /**
     * Shortcut for log + T_DEBUG call.
     *
     * @param   string|\Exception   $notification       Either short message or exception to log.
     * @param   array               $data               Optional additional fields to set. See also 'setValue' method.
     * @param   string              $facility           Optional facility name eg. application name. See also 'setValue' method.
     */
    public static function debug($notification, $data = array(), $facility = '')
    {
        static::getInstance()->log(self::T_DEBUG, $notification, $data, $facility);
    }

    /**
     * Log a message to the configured writers.
     *
     * @param   int                 $level              Log level.
     * @param   string|\Exception   $notification       Either short message or exception to log.
     * @param   array               $data               Optional additional fields to set. See also 'setValue' method.
     * @param   string              $facility           Optional facility name eg. application name. See also 'setValue' method.
     */
    public function log($level, $notification, $data = array(), $facility = '')
    {
        if (isset($this->writers[$level]) && count($this->writers[$level]) > 0) {
            if (is_scalar($notification) && $notification != '') {
                $message   = $notification;
                $exception = null;

                // fetch line and file from debug backtrace
                $trace = debug_backtrace(0);

                while (count($trace) > 1 && isset($trace[0]['class']) && $trace[0]['class'] == __CLASS__) {
                    array_shift($trace);
                }

                if (count($trace) > 0) {
                    $line = $trace[0]['line'];
                    $code = 0;
                    $file = $trace[0]['file'];
                } else {
                    $line = 0;
                    $code = 0;
                    $file = '';
                }
            } elseif ($notification instanceof \Exception || $notification instanceof \octris\core\logger\message) {
                $message   = $notification->getMessage();
                $exception = ($notification instanceof \Exception
                                ? $notification
                                : null);

                // fetch line, code and file from backtrace if no exception is specified
                $line = $notification->getLine();
                $file = $notification->getFile();
                $code = $notification->getCode();
            } else {
                throw new \Exception("'notification' must either be a text message or an 'Exception'");
            }

            $tmp = array(
                'host'      => gethostname(),
                'timestamp' => microtime(true),
                'message'   => $message,
                'facility'  => ($facility ? $facility : $this->facility),
                'exception' => $exception,
                'level'     => $level,
                'line'      => $line,
                'file'      => $file,
                'code'      => $code,
                'data'      => array_merge($this->data, $data)
            );

            foreach ($this->writers[$level] as $l) {
                try {
                    $l->write($tmp);
                } catch (\Exceptions $e) {
                    // make sure, that one failing logger will not prevent writing to other loggers
                }
            }
        }
    }
}
