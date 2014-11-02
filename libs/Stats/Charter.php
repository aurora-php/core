<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Stats;

/**
 * Client library for Charter:
 *
 * http://pbosetti.github.com/Charter/
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Charter
{
    /**
     * Numerical Id of graph.
     *
     * @type    int
     */
    protected $id;

    /**
     * Host Charter is listening on.
     *
     * @type    string
     */
    protected $host;

    /**
     * Port Charter is listening on.
     *
     * @type    int
     */
    protected $port;

    /**
     * Sample-rate for sampling.
     *
     * @type    float
     */
    protected $sample_rate;

    /**
     * Constructor.
     *
     * @param   int             $id                 Numerical Id of graph.
     * @param   string          $host               Optional host.
     * @param   int             $base_port          Optional start of port-range Charter is listening on.
     * @param   float           $sample_rate        Optional sampling-rate (0 - 1).
     */
    public function __construct($id, $host = '127.0.0.1', $base_port = 2000, $sample_rate = 1)
    {
        $this->id   = $id;
        $this->host = $host;
        $this->port = $base_port + $id;

        $this->sample_rate = $sample_rate;
    }

    /**
     * Deliver UDP message to Charter.
     *
     * @param   string          $msg                Message to send to Charter using UDP.
     */
    protected function deliver($msg)
    {
        if ((mt_rand() / mt_getrandmax()) <= $this->sample_rate) {
            $sock = stream_socket_client('udp://' . $this->host . ':' . $this->port);
            fwrite($sock, $msg);
            fclose($sock);
        }
    }

    /**
     * Send clear command.
     */
    public function clear()
    {
        $this->deliver('CLEAR');
    }

    /**
     * Send close command.
     */
    public function close()
    {
        $this->deliver('CLOSE');
    }

    /**
     * Send arbitrary numerical data to Charter.
     */
    public function send(array $data)
    {
        $msg = 's ' . implode(' ', array_filter($data, function ($v) {
            return (is_numeric($v));
        }));

        $this->deliver($msg);
    }

    /**
     * Send names to Charter.
     */
    public function names($names)
    {
        if (count($names) > 0) {
            $this->deliver('NAMES ' . implode(' ', $names));
        }
    }

    /**
     * Send labels to Charter.
     */
    public function labels(array $labels)
    {
        if (count($labels) > 0) {
            $this->deliver('LABELS ' . implode(' ', $labels));
        }
    }
}
