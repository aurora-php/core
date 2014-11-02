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
 * Logger to send messages to FirePHP.
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Firephp implements \Octris\Core\Logger\IWriter
{
    /**
     * Wildfire JSON streaming protocol header URI.
     *
     * @type    string
     */
    private static $protocol_uri = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2';

    /**
     * FirePHP structure header URI.
     *
     * @type    string
     */
    private static $structure_uri = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';

    /**
     * Plugin header URI.
     *
     * @type    string
     */
    private static $plugin_uri = 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3';

    /**
     * Header prefix as required by Wildfire protocol.
     *
     * @type    string
     */
    private static $prefix = 'X-Wf';

    /**
     * Mapping of logger levels to FirePHP level types.
     *
     * @type    array
     */
    private static $level_types = array(
        \Octris\Core\Logger::T_EMERGENCY => 'ERROR',
        \Octris\Core\Logger::T_ALERT     => 'ERROR',
        \Octris\Core\Logger::T_CRITICAL  => 'ERROR',
        \Octris\Core\Logger::T_ERROR     => 'ERROR',
        \Octris\Core\Logger::T_WARNING   => 'WARN',
        \Octris\Core\Logger::T_NOTICE    => 'INFO',
        \Octris\Core\Logger::T_INFO      => 'INFO',
        \Octris\Core\Logger::T_DEBUG     => 'LOG',
    );

    /**
     * Mapping of logger levels to textual names.
     *
     * @type    array
     */
    private static $level_names = array(
        \Octris\Core\Logger::T_EMERGENCY => 'emergency',
        \Octris\Core\Logger::T_ALERT     => 'alert',
        \Octris\Core\Logger::T_CRITICAL  => 'critical',
        \Octris\Core\Logger::T_ERROR     => 'error',
        \Octris\Core\Logger::T_WARNING   => 'warning',
        \Octris\Core\Logger::T_NOTICE    => 'notice',
        \Octris\Core\Logger::T_INFO      => 'info',
        \Octris\Core\Logger::T_DEBUG     => 'debug'
    );

    /**
     * Whether the Wildfire specific headers have been send.
     *
     * @type    bool
     */
    protected static $initialized = false;

    /**
     * Sequence number of message to send to FirePHP.
     *
     * @type    int
     */
    protected static $seq_num = 1;

    /**
     * Maximum chunk size for JSON stream messages.
     *
     * @type    int
     */
    protected $chunk_size = 4096;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Create header for FirePHP.
     *
     * @param   array       $meta           Meta-information for header.
     * @param   string      $value          Value to set for header.
     */
    protected function createHeader(array $meta, $value)
    {
        header(sprintf('%s-%s: %s', self::$prefix, implode('-', $meta), $value));
    }

    /**
     * Create JSON Stream for data.
     *
     * @param   string      $type           Message type.
     * @param   string      $file           Name of file the message was issued in.
     * @param   int         $line           Number of line the message was issued in.
     * @param   string      $label          Label of message.
     * @param   array       $data           Data to wrap in a JSON stream.
     */
    public function createJsonStream($type, $file, $line, $label, array $data)
    {
        $data = json_encode(
            array(
                array(
                    'Type'  => $type,
                    'File'  => $file,
                    'Line'  => $line,
                    'Label' => $label
                ),
                $data
            )
        );

        $size = strlen($data);          // size in bytes and not in characters

        if ($size > $this->chunk_size) {
            $parts = str_split($size . $data, $this->chunk_size);

            $this->createHeader(
                array(1, 1, 1, static::$seq_num++),
                $size . '|' . $parts[0] . '|\\'
            );

            for ($i = 1, $cnt = count($parts); $i < ($cnt - 1); ++$i) {
                $this->createHeader(
                    array(1, 1, 1, static::$seq_num++),
                    '|' . $parts[$i] . '|\\'
                );
            }

            $this->createHeader(
                array(1, 1, 1, static::$seq_num++),
                '|' . $parts[$cnt - 1] . '|'
            );
        } else {
            $this->createHeader(
                array(1, 1, 1, static::$seq_num++),
                $size . '|' . $data . '|'
            );
        }
    }

    /**
     * Send logging message to a FirePHP.
     *
     * @param   array       $message        Message to send.
     */
    public function write(array $message)
    {
        if (!static::$initialized) {
            // this is the first call to write, wildfire headers have to be send first
            $this->createHeader(array('Protocol', 1), self::$protocol_uri);
            $this->createHeader(array(1, 'Structure', 1), self::$structure_uri);
            $this->createHeader(array(1, 'Plugin', 1), self::$plugin_uri);

            static::$initialized = true;
        }

        // send message summary
        $this->createJsonStream(
            self::$level_types[$message['level']],
            $message['file'],
            $message['line'],
            $message['message'],
            array(
                'file'      => $message['file'],
                'line'      => $message['line'],
                'code'      => $message['code'],
                'message'   => $message['message'],
                'host'      => $message['host'],
                'level'     => self::$level_names[$message['level']],
                'time'      => sprintf(
                    '%s.%d',
                    strftime(
                        '%Y-%m-%d %H:%M:%S',
                        $message['timestamp']
                    ),
                    substr(strstr($message['timestamp'], '.'), 1)
                ),
                'facility'  => $message['facility'],
                'data'      => $message['data']
            )
        );
    }
}
