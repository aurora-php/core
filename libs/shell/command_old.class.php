<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app\cli {
    /**
     * Command execution class.
     *
     * @octdoc      c:cli/command
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class command
    /**/
    {
        /**
         * Resource types.
         *
         * @octdoc  d:command/T_STDIN, T_STDOUT, T_STDERR
         * @type    string
         */
        const T_STDIN  = 0;
        const T_STDOUT = 1;
        const T_STDERR = 2;
        /**/

        /**
         * Command to execute.
         *
         * @octdoc  p:command/$cmd
         * @type    string
         */
        protected $cmd;
        /**/

        /**
         * Working directory.
         *
         * @octdoc  p:command/$cwd
         * @type    string|null
         */
        protected $cwd;
        /**/

        /**
         * Environment variables.
         *
         * @octdoc  p:command/$env
         * @type    array
         */
        protected $env;
        /**/

        /**
         * Additional options.
         *
         * @octdoc  p:command/$options
         * @type    array
         */
        protected $options;
        /**/

        /**
         * Delay to nice CPU.
         *
         * @octdoc  p:command/$delay
         * @type    int
         */
        protected $delay = 100;
        /**/

        /**
         * File modes and read/write bit mapping:
         *
         * bit 1 - reading is allowed
         * bit 2 - writing is allowed
         *
         * @octdoc  p:command/$modes
         * @type    array
         */
        private static $modes = array(
            'r'  => 1, 'r+' => 3,
            'w'  => 2, 'w+' => 3,
            'a'  => 2, 'a+' => 3,
            'x'  => 2, 'x+' => 3,
            'c'  => 2, 'c+' => 3
        );
        /**/

        /**
         * Descriptors.
         *
         * @octdoc  p:command/$descriptors
         * @type    array
         */
        protected $descriptors = array(
            self::T_STDIN  => array('pipe', 'r'),
            self::T_STDOUT => array('file', '/dev/null', 'w'),
            self::T_STDERR => array('file', '/dev/null', 'w')
        );
        /**/

        /**
         * Resources.
         *
         * @octdoc  p:command/$callbacks
         * @type    array
         */
        protected $callbacks = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:command/__construct
         * @param   string                  $cmd                    Command to execute.
         */
        public function __construct($cmd, $cwd = null, $env = array(), $options = array())
        /**/
        {
            $this->cmd     = $cmd;
            $this->cwd     = $cwd;
            $this->env     = $env;
            $this->options = $options;
        }

        /**
         * Set descriptor configuration. The parameter 'arg' may eithor contain a callable callback, a file resource .
         *
         * @octdoc  m:command/setStdin
         * @param   int                         $type               Descriptor number to add set configuration for.
         * @param   resource|string|callable    $arg                A resource, a string, a filename or a callback to configure for descriptor.
         * @param   bool                        $mode               Optional filemode to use if a filename was specified.
         */
        public function setDescriptor($type, $arg, $append = false)
        /**/
        {
            if ($type !== self::T_STDIN && $type !== self::T_STDOUT && $type !== self::T_STDERR) {
                throw new \Exception('Invalid descriptor type "' . $type . '".');
            }

            if (is_string($arg)) {
                // either a string or a filename
                if (substr($arg, 0, 7) == 'file://') {
                    // filename specified
                    $arg = substr($arg, 7);

                    if ($type == self::T_STDIN && !is_readable($arg)) {
                        throw new \Exception('File is not readable "' . $arg . '".');
                    } elseif (!is_writable($arg)) {
                        throw new \Exception('File is not writable "' . $arg . '".');
                    }

                    unset($this->callbacks[$type]);
                    $this->descriptors[$type] = array('file', $arg, ($type == self::T_STDIN ? 'r' : ($append ? 'a' : 'w')));
                } elseif ($type != self::T_STDIN) {
                    throw new \Exception('A string is only valid for STDIN');
                } else {
                    // a string for STDIN
                    $this->descriptors[$type] = array('pipe', 'r');
                    $this->callbacks[$type]   = function($idx) use ($arg) {
                        return ($idx == 0 ? $arg : null);
                    };
                }
            } elseif (is_callable($arg)) {
                $this->descriptors[$type] = array('pipe', ($type == self::T_STDIN ? 'r' : 'w'));
                $this->callbacks[$type]   = $arg;
            } elseif (is_resource($arg)) {
                $mode = stream_get_meta_data($resource)['mode'];

                if ($type == self::T_STDIN) {
                    if ((self::$modes[$mode] & 1) != 1) {
                        throw new \Exception('Resource is not readable.');
                    } else {
                        $this->descriptors = array('pipe', 'r');
                        $this->callbacks   = function() use (&$arg) {
                            return (!feof($arg) ? fgets($arg) : null);
                        };
                    }
                } else {
                    if ((self::$modes[$mode] & 2) != 2) {
                        throw new \Exception('Resource is not writable.');
                    } else {
                        $this->descriptors = array('pipe', 'w');
                        $this->callbacks   = function($row) use (&$arg) {
                            fputs($arg, $row);
                        };
                    }
                }
            } else {
                throw new \Exception('Resource of type "' . gettype($resource) . '" is not allowed.');
            }
        }

        /**
         * Execute command.
         *
         * @octdoc  m:command/exec
         * @return  int                                             Exit code of command.
         */
        public function exec()
        /**/
        {
            // execute command
            $pipes     = array();
            $exitcode  = null;
            $callbacks = $this->callbacks;

            if (!($proc = proc_open($this->cmd, $this->descriptors, $pipes, $this->cwd, $this->env, $this->options))) {
                throw new \Exception('Unable to run command');
            }

            // send STDIN to the process
            if (isset($pipes[self::T_STDIN])) {
                if (isset($callbacks[self::T_STDIN])) {
                    $idx = 0;

                    while (($row = $callbacks[self::T_STDIN]($idx++)) !== null) {
                        fputs($pipes[self::T_STDIN], $row);
                    }
                }

                fclose($pipes[self::T_STDIN]);
                unset($pipes[self::T_STDIN]);
            }

            // set non-blocking mode for SSTDOUT and STDERR
            foreach ($pipes as $type => $resource) {
                stream_set_blocking($resource, 0);
            }

            do {
                if (is_null($exitcode)) {
                    $status = proc_get_status($proc);

                    if (!$status['running']) $exitcode = $status['exitcode'];
                }

                foreach ($pipes as $type => $resource) {
                    if (feof($resource)) {
                        fclose($resource);
                        unset($pipes[$type]);

                        break;
                    } else {
                        $data = fgets($resource);

                        if (strlen($data) > 0) {
                            if (isset($callbacks[$type])) {
                                if ($callbacks[$type]($data) === false) {
                                    fclose($resource);
                                    unset($pipes[$type]);

                                    break;
                                }
                            }
                        } else {
                            usleep($this->delay);
                        }
                    }
                }
            } while (count($pipes) > 0);

            if (proc_get_status($proc)['running']) {
                // terminate process, if it's still running
                proc_terminate($proc);
            }

            $tmp = proc_close($proc);

            return (is_null($exitcode) ? $tmp : $exitcode);
        }
    }
}
