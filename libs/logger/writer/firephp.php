<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\logger\writer {
    /**
     * Logger to send messages to FirePHP.
     *
     * @octdoc      c:writer/firephp
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class firephp implements \org\octris\core\logger\writer_if
    /**/
    {
        /**
         * Wildfire JSON streaming protocol header URI.
         *
         * @octdoc  p:firephp/$protocol_uri
         * @type    string
         */
        private static $protocol_uri = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2';
        /**/

        /**
         * FirePHP structure header URI.
         *
         * @octdoc  p:firephp/$structure_uri
         * @type    string
         */
        private static $structure_uri = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
        /**/

        /**
         * Plugin header URI.
         *
         * @octdoc  p:firephp/$plugin_uri
         * @type    string
         */
        private static $plugin_uri = 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3';
        /**/

        /**
         * Header prefix as required by Wildfire protocol.
         *
         * @octdoc  p:firephp/$prefix
         * @type    string
         */
        private static $prefix = 'X-Wf';
        /**/

        /**
         * Mapping of logger levels to FirePHP level types.
         *
         * @octdoc  p:firephp/$level_types
         * @type    array
         */
        private static $level_types = array(
            \org\octris\core\logger::T_EMERGENCY => 'ERROR',
            \org\octris\core\logger::T_ALERT     => 'ERROR',
            \org\octris\core\logger::T_CRITICAL  => 'ERROR',
            \org\octris\core\logger::T_ERROR     => 'ERROR',
            \org\octris\core\logger::T_WARNING   => 'WARN',
            \org\octris\core\logger::T_NOTICE    => 'INFO',
            \org\octris\core\logger::T_INFO      => 'INFO',
            \org\octris\core\logger::T_DEBUG     => 'LOG',
        );
        /**/

        /**
         * Mapping of logger levels to textual names.
         *
         * @octdoc  p:file/$level_names
         * @type    array
         */
        private static $level_names = array(
            \org\octris\core\logger::T_EMERGENCY => 'emergency',
            \org\octris\core\logger::T_ALERT     => 'alert',
            \org\octris\core\logger::T_CRITICAL  => 'critical',
            \org\octris\core\logger::T_ERROR     => 'error',
            \org\octris\core\logger::T_WARNING   => 'warning',
            \org\octris\core\logger::T_NOTICE    => 'notice',
            \org\octris\core\logger::T_INFO      => 'info',
            \org\octris\core\logger::T_DEBUG     => 'debug'
        );
        /**/

        /**
         * Whether the Wildfire specific headers have been send.
         *
         * @octdoc  p:firephp/$initialized
         * @type    bool
         */
        protected static $initialized = false;
        /**/

        /**
         * Sequence number of message to send to FirePHP.
         *
         * @octdoc  p:firephp/$seq_num
         * @type    int
         */
        protected static $seq_num = 1;
        /**/

        /**
         * Maximum chunk size for JSON stream messages.
         *
         * @octdoc  p:firephp/$chunk_size
         * @type    int
         */
        protected $chunk_size = 4096;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:firephp/__construct
         */
        public function __construct()
        /**/
        {
        }

        /**
         * Create header for FirePHP.
         *
         * @octdoc  m:firephp/createHeader
         * @param   array       $meta           Meta-information for header.
         * @param   string      $value          Value to set for header.
         */
        protected function createHeader(array $meta, $value)
        /**/
        {
            header(sprintf('%s-%s: %s', self::$prefix, implode('-', $meta), $value));
        }

        /**
         * Create JSON Stream for data.
         *
         * @octdoc  m:firephp/packData
         * @param   string      $type           Message type.
         * @param   string      $file           Name of file the message was issued in.
         * @param   int         $line           Number of line the message was issued in.
         * @param   string      $label          Label of message.
         * @param   array       $data           Data to wrap in a JSON stream.
         */
        public function createJsonStream($type, $file, $line, $label, array $data)
        /**/
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
         * @octdoc  m:firephp/write
         * @param   array       $message        Message to send.
         */
        public function write(array $message)
        /**/
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
                        substr(strstr($message['timestamp'], '.' ), 1)
                    ),
                    'facility'  => $message['facility'],
                    'data'      => $message['data']
                )
            );
        }
    }
}
