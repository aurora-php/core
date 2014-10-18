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
     * StatsD client class. This class is derived from the official php client class at: https://github.com/etsy/statsd/blob/master/examples/php-example.php
     *
     * @octdoc      c:stats/statsd
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class statsd
    /**/
    {
        /**
         * Host the statsd server is listening on.
         *
         * @octdoc  p:statsd/$host
         * @type    string
         */
        protected $host;
        /**/
        
        /**
         * Port the statsd server is listening on.
         *
         * @octdoc  p:statsd/$port
         * @type    int
         */
        protected $port;
        /**/

        /**
         * Default sampling-rate.
         *
         * @octdoc  p:statsd/$sampling_rate
         * @type    float
         */
        protected $sampling_rate;
        /**/    

        /**
         * Constructor.
         *
         * @octdoc  m:statsd/__construct
         * @param   string          $host               Optional host.
         * @param   int             $base_port          Optional port statsd server is listening on.
         * @param   float           $sample_rate        Optional default sampling-rate (0 - 1).
         */
        public function __construct($host = '127.0.0.1', $port = 8125, $sampling_rate = 1)
        /**/
        {
            $this->host = $host;
            $this->port = $port;

            $this->sampling_rate = $sampling_rate;
        }

        /**
         * Log timing information.
         *
         * @octdoc  m:statsd/timing
         * @param   string          $metric             The metric to log timing info for.
         * @param   float           $time               Ellapsed time in microseconds to log.
         * @param   float           $sample_rate        Optional sampling-rate (0 - 1) to overwrite default sampling-rate.
         */
        public function timing($metric, $time, $sampling_rate = null)
        /**/
        {
            $this->send(array($metric => $time . '|ms'), $sampling_rate);
        }

        /**
         * Increment one or multiple counters.
         *
         * @octdoc  m:statsd/incr
         * @param   array|string    $metrics            The metric(s) to increment.
         * @param   float           $sample_rate        Optional sampling-rate (0 - 1) to overwrite default sampling-rate.
         */
        public function incr($metrics, $sampling_rate = null)
        /**/
        {
            $this->update($metrics, 1, $sampling_rate);
        }

        /**
         * Decrement one or multiple counters.
         *
         * @octdoc  m:statsd/decr
         * @param   array|string    $metrics            The metric(s) to decrement.
         * @param   float           $sample_rate        Optional sampling-rate (0 - 1) to overwrite default sampling-rate.
         */
        public function decr($metrics, $sampling_rate = null)
        /**/
        {
            $this->update($metrics, -1, $sampling_rate);
        }

        /**
         * Update one or multiple counters by arbitrary amounts.
         *
         * @octdoc  m:statds/update
         * @param   array|string    $metrics            The metric(s) to decrement.
         * @param   int             $value              Value to increment or decrement metric(s) with.
         * @param   float           $sample_rate        Optional sampling-rate (0 - 1) to overwrite default sampling-rate.
         */
        public function update($metrics, $value, $sampling_rate = null)
        /**/
        {
            if (!is_array($metrics)) $metrics = array($metrics);

            $data = array_map(function($v) {
                return $v . '|c';
            }, $metrics);

            $this->send($data, $sampling_rate);
        }

        /**
         * Send data to statsd server using UDP.
         *
         * @octdoc  m:statsd/send
         * @param   array           $data               Data to send to statsd server.
         * @param   float           $sample_rate        Optional sampling-rate (0 - 1) to overwrite default sampling-rate.
         */
        protected function send(array $data, $sampling_rate = null)
        /**/
        {
            $sampling_rate = (is_null($sampling_rate)
                              ? $this->sampling_rate
                              : $sampling_rate);

            if ($sampling_rate < 1) {
                $data = array_map(
                    function($v) use ($sampling_rate) {
                        return $v . '|@' . $sampling_rate;
                    }, 
                    array_filter(
                        $data, 
                        function($v) use ($sampling_rate) {
                            return ((mt_rand() / mt_getrandmax()) <= $sampling_rate);
                        }
                    )
                );
            }

            if (!empty($data)) {
                $sock = stream_socket_client('udp://' . $this->host . ':' . $this->port);

                foreach ($data as $metric => $value) {
                    fwrite($sock, $metric . ':' . $value);
                }

                fclose($sock);
            }
        }   
    }
}
