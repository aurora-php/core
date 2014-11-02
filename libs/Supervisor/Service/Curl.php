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
 * cURL wrapper class as a service for the octris supervisor class.
 *
 * @copyright   Copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Net implements \Octris\Core\Supervisor\IService
{
    /**
     * Curl multi handle, if currently executed, otherwise null.
     *
     * @type    resource|null
     */
    protected $mh = null;

    /**
     * Max. concurrent sessions.
     *
     * @type    int
     */
    protected $concurrency = 10;

    /**
     * The clients.
     *
     * @type    array
     */
    protected $clients = array();

    /**
     * Session queue.
     *
     * @type    array
     */
    protected $queue = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Set number of concurrent threads.
     *
     * @param   int             $concurrency                       Maximum concurrent sessions.
     */
    public function setConcurrency($concurrency)
    {
        $this->concurrency = $concurrency;
    }

    /**
     * Add a network transport client to the session.
     *
     * @param   \Octris\Core\Net\Client     $client         Client to add to session.
     * @return  \octris\core\net\client                     The client instance.
     */
    public function addClient(\Octris\Core\Net\Client $client)
    {
        if (is_null($this->mh)) {
            $this->clients[] = $client;
        } else {
            // add new client directly to the queue when clients get already executed
            $ch = curl_init();
            curl_setopt_array($ch, $client->getOptions());

            curl_multi_add_handle($this->mh, $ch);
        }

        return $client;
    }

    /**
     * Poll
     */
    public function poll()
    {

    }

    /**
     * Execute registered clients.
     */
    public function execute()
    {
        if (!is_null($this->mh)) {
            throw new \Execution('Session is currently beeing executed');
        }

        $this->queue = $this->clients;

        $active  = null;
        $clients = array();

        $this->mh = curl_multi_init();

        $push_clients = function ($init = 0) use (&$clients) {
            for ($i = $init, $cnt = 0; $i < $this->concurrency; ++$i) {
                if (!($client = array_shift($this->queue))) {
                    break;
                }

                $ch = curl_init();
                curl_setopt_array($ch, $client->getOptions());

                curl_multi_add_handle($this->mh, $ch);

                $clients[(string)$ch] = $client;

                ++$cnt;
            }

            return $cnt;
        };
        $push_clients();

        do {
            curl_multi_select($this->mh);

            do {
                $result = curl_multi_exec($this->mh, $active);
            } while ($result == CURLM_CALL_MULTI_PERFORM);

            if ($result != CURLM_OK) {
                break;
            }

            while (($done = curl_multi_info_read($this->mh, $remain))) {
                // handle result of requests
                $key = (string)$done['handle'];

                if ($done['msg']  == CURLMSG_DONE) {
                    $listener = $clients[$key]->getListener();

                    if (!is_null($listener)) {
                        $listener(curl_multi_getcontent($done['handle']));
                    }
                }

                unset($clients[$key]);
            }

            // add remaining clients
            $pushed = $push_clients($active);
        } while ($active > 0 || count($this->queue) > 0 || $pushed > 0);

        curl_multi_close($this->mh);
        $this->mh = null;
    }
}
