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
     * Logger to write messages to a file.
     *
     * @octdoc      c:writer/file
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class file implements \org\octris\core\logger\writer_if
    /**/
    {
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
         * Name of file to log to.
         *
         * @octdoc  p:file/$filename
         * @type    string
         */
        protected $filename;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:file/__construct
         * @param   string      $filename       Name of file to log to.
         */
        public function __construct($filename)
        /**/
        {
            $this->filename = $filename;
        }

        /**
         * Write logging message to a file.
         *
         * @octdoc  m:file/write
         * @param   array       $message        Message to send.
         */
        public function write(array $message)
        /**/
        {
            if (!($fp = fopen($this->filename, 'w'))) {
                // error handling
            } else {
                if (($is_html = ($this->filename == 'php://output' && php_sapi_name() != 'cli'))) {
                    fwrite($fp, '<pre>');
                }

                fwrite($fp, "MESSAGE\n");
                fwrite($fp, sprintf("  id      : %s\n", md5(serialize($message))));
                fwrite($fp, sprintf("  message : %s\n", $message['message']));
                fwrite($fp, sprintf("  host    : %s\n", $message['host']));
                fwrite($fp, sprintf("  time    : %s.%d\n", strftime('%Y-%m-%d %H:%M:%S', $message['timestamp']), substr(strstr($message['timestamp'], '.' ), 1)));
                fwrite($fp, sprintf("  level   : %s\n", self::$level_names[$message['level']]));
                fwrite($fp, sprintf("  facility: %s\n", $message['facility']));
                fwrite($fp, sprintf("  file    : %s\n", $message['file']));
                fwrite($fp, sprintf("  line    : %d\n", $message['line']));
                fwrite($fp, sprintf("  code    : %d\n", $message['code']));

                if (count($message['data']) > 0) {
                    fwrite($fp, "DATA\n");

                    $max = 0;
                    array_walk($message['data'], function($v, $k) use (&$max) {
                        $max = max(strlen($k), $max);
                    });

                    foreach ($message['data'] as $k => $v) {
                        fwrite($fp, sprintf(
                            "  %-" . $max . "s: %s\n",
                            $k,
                            (!is_scalar($v) ? json_encode($v) : $v)
                        ));
                    }
                }

                if (!is_null($message['exception'])) {
                    fwrite($fp, "TRACE\n");
                    fwrite($fp, preg_replace('/^/m', '  ', $message['exception']->getTraceAsString()));
                }

                fwrite($fp, "\n");

                if ($is_html) {
                    fwrite($fp, '</pre>');
                }

                fclose($fp);
            }
        }
    }
}
