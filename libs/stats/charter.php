<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\stats {
    /**
     * Client library for Charter:
     *
     * http://pbosetti.github.com/Charter/
     *
     * @octdoc      c:stats/charter
     * @copyright   copyright (c) 2011-2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class charter
    /**/
    {
        /**
         * Numerical Id of graph.
         *
         * @octdoc  p:charter/$id
         * @type    int
         */
        protected $id;
        /**/

        /**
         * Host Charter is listening on.
         *
         * @octdoc  p:charter/$host
         * @type    string
         */
        protected $host;
        /**/

        /**
         * Port Charter is listening on.
         *
         * @octdoc  p:charter/$port
         * @type    int
         */
        protected $port;
        /**/
        
        /**
         * Sample-rate for sampling.
         *
         * @octdoc  p:charter/$sample_rate
         * @type    float
         */
        protected $sample_rate;

        /**
         * Constructor.
         *
         * @octdoc  m:charter/__construct
         * @param   int             $id                 Numerical Id of graph.
         * @param   string          $host               Optional host.
         * @param   int             $base_port          Optional start of port-range Charter is listening on.
         * @param   float           $sample_rate        Optional sampling-rate (0 - 1).
         */
        public function __construct($id, $host = '127.0.0.1', $base_port = 2000, $sample_rate = 1)
        /**/
        {
            $this->id   = $id;
            $this->host = $host;
            $this->port = $base_port + $id;

            $this->sample_rate = $sample_rate;
        }
        
        /**
         * Deliver UDP message to Charter.
         *
         * @octdoc  m:charter/deliver
         * @param   string          $msg                Message to send to Charter using UDP.
         */
        protected function deliver($msg)
        /**/
        {
            if ((mt_rand() / mt_getrandmax()) <= $this->sample_rate) {
                $sock = stream_socket_client('udp://' . $this->host . ':' . $this->port);
                fwrite($sock, $msg);
                fclose($sock);
            }
        }
        
        /**
         * Send clear command.
         *
         * @octdoc  m:charter/clear
         */
        public function clear()
        /**/
        {
            $this->deliver('CLEAR');
        }

        /**
         * Send close command.
         *
         * @octdoc  m:charter/close
         */
        public function close()
        /**/
        {
            $this->deliver('CLOSE');
        }
        
        /**
         * Send arbitrary numerical data to Charter.
         *
         * @octdoc  m:charter/send
         */
        public function send(array $data)
        /**/
        {
            $msg = 's ' . implode(' ', array_filter($data, function($v) {
                return (is_numeric($v));
            }));
            
            $this->deliver($msg);
        }
        
        /**
         * Send names to Charter.
         *
         * @octdoc  m:charter/names
         */
        public function names($names)
        /**/
        {
            if (count($names) > 0) {
                $this->deliver('NAMES ' . implode(' ', $names));
            }
        }

        /**
         * Send labels to Charter.
         *
         * @octdoc  m:charter/labels
         */
        public function labels(array $labels) 
        /**/
        {
            if (count($labels) > 0) {
                $this->deliver('LABELS ' . implode(' ', $labels));
            }
        }
    }
}
