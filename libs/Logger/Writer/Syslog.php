<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Logger\Writer;

/**
 * Logger to write messages to syslog.
 *
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Syslog implements \Octris\Core\Logger\IWriter
{
    /**
     * Syslog facilities.
     *
     * * T_AUTH -- security/authorization messages
     * * T_AUTHPRIV -- security/authorization messages (private)
     * * T_CRON -- clock daemon (cron and at)
     * * T_DAEMON -- other system daemons
     * * T_KERN -- kernel messages
     * * T_LOCAL0 ... LOG_LOCAL7 -- reserved for local use
     * * T_LPR -- line printer subsystem
     * * T_MAIL -- mail subsystem
     * * T_NEWS -- usenet news subsystem
     * * T_SYSLOG -- messages generated internally by syslogd
     * * T_USER -- generic user-level messages
     * * T_UUCP -- UUCP subsystem
     *
     */
    const T_AUTH     = 'LOG_AUTH';
    const T_AUTHPRIV = 'LOG_AUTHPRIV';
    const T_CRON     = 'LOG_CRON';
    const T_DAEMON   = 'LOG_DAEMON';
    const T_KERN     = 'LOG_KERN';
    const T_LOCAL0   = 'LOG_LOCAL0';
    const T_LOCAL1   = 'LOG_LOCAL1';
    const T_LOCAL2   = 'LOG_LOCAL2';
    const T_LOCAL3   = 'LOG_LOCAL3';
    const T_LOCAL4   = 'LOG_LOCAL4';
    const T_LOCAL5   = 'LOG_LOCAL5';
    const T_LOCAL6   = 'LOG_LOCAL6';
    const T_LOCAL7   = 'LOG_LOCAL7';
    const T_LPR      = 'LOG_LPR';
    const T_MAIL     = 'LOG_MAIL';
    const T_NEWS     = 'LOG_NEWS';
    const T_SYSLOG   = 'LOG_SYSLOG';
    const T_USER     = 'LOG_USER';
    const T_UUCP     = 'LOC_UUCP';
    
    /**
     * For internal usage only.
     *
     * @type    array
     */
    private static $facilities = null;
    
    /**
     * Mapping of logger levels to syslog levels.
     *
     * @type    array
     */
    private static $syslog_levels = array(
        \Octris\Core\Logger::T_EMERGENCY => LOG_EMERG,
        \Octris\Core\Logger::T_ALERT     => LOG_ALERT,
        \Octris\Core\Logger::T_CRITICAL  => LOG_CRIT,
        \Octris\Core\Logger::T_ERROR     => LOG_ERR,
        \Octris\Core\Logger::T_WARNING   => LOG_WARNING,
        \Octris\Core\Logger::T_NOTICE    => LOG_NOTICE,
        \Octris\Core\Logger::T_INFO      => LOG_INFO,
        \Octris\Core\Logger::T_DEBUG     => LOG_DEBUG
    );
    
    /**
     * Syslog was opened.
     *
     * @type    bool
     */
    private static $is_open = false;
    
    /**
     * Last facility that wrote to syslog.
     *
     * @type    string
     */
    private static $last_facility = '';
    
    /**
     * Syslog facility.
     *
     * @type    string
     */
    protected $facility;
    
    /**
     * Constructor.
     *
     * @param   int         $facility       Optional syslog facility.
     */
    public function __construct($facility = self::T_USER)
    {
        if (is_null(self::$facilities)) {
            $r = new \ReflectionClass(get_class($this));
            self::$facilities = $r->getConstants();
        }

        if (!in_array($facility, self::$facilities)) {
            throw new \Exception(sprintf('Unknown facility "%s"', $facility));
        } elseif (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' && $facility != self::T_USER) {
            throw new \Exception('Windows only supports the facility "T_USER"');
        } elseif (!defined($facility)) {
            throw new \Exception(sprintf(
                'Operating system does not support facility "%s"',
                array_search($facility, $this->facilities)
            ));
        }

        $this->facility = $facility;
    }

    /**
     * Destructor.
     *
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Open syslog for configured facility.
     *
     */
    public function open()
    {
        $this->close();

        if (!(openlog('', LOG_PID, constant($this->facility)))) {
            throw new \Exception(sprintf(
                'Unable to open syslog for facility "%s"',
                $this->facility
            ));
        }

        self::$is_open       = true;
        self::$last_facility = $this->facility;
    }

    /**
     * Close syslog.
     *
     */
    public function close()
    {
        if (self::$is_open) {
            closelog();
        }
    }

    /**
     * Write logging message to syslog
     *
     * @param   array       $message        Message to send.
     */
    public function write(array $message)
    {
        if (!self::$is_open || self::$facility != $this->facility) {
            $this->open();
        }

        syslog(
            self::$syslog_levels[$message['level']],
            sprintf(
                '%s%s(%d) **%s**',
                ($message['facility']
                    ? $message['facility'] . ': '
                    : ''),
                $message['file'],
                $message['line'],
                $message['message']
            )
        );
    }
}
