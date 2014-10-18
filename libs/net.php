<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * cURL wrapper class.
     * 
     * @octdoc      c:core/net
     * @copyright   Copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class net
    /**/
    {
        /**
         * Curl multi handle, if currently executed, otherwise null.
         *
         * @octdoc  p:net/$mh
         * @type    resource|null
         */
        protected $mh = null;
        /**/

        /**
         * Max. concurrent sessions.
         *
         * @octdoc  p:net/$concurrency
         * @type    int
         */
        protected $concurrency = 10;
        /**/

        /**
         * The clients.
         *
         * @octdoc  p:net/$clients
         * @type    array
         */
        protected $clients = array();
        /**/

        /**
         * Session queue.
         *
         * @octdoc  p:net/$queue
         * @type    array
         */
        protected $queue = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:net/__construct
         */
        public function __construct()
        /**/
        {
        }

        /**
         * Set number of concurrent threads.
         *
         * @octdoc  m:net/setConcurrency
         * @param   int             $concurrency                       Maximum concurrent sessions.
         */
        public function setConcurrency($concurrency)
        /**/
        {
            $this->concurrency = $concurrency;
        }

        /**
         * Add a network transport client to the session.
         *
         * @octdoc  m:net/addClient
         * @param   \org\octris\core\net\client     $client         Client to add to session.
         * @return  \org\octris\core\net\client                     The client instance.
         */
        public function addClient(\org\octris\core\net\client $client)
        /**/
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
         * Execute registered clients.
         *
         * @octdoc  m:net/execute
         */
        public function execute()
        /**/
        {
            if (!is_null($this->mh)) {
                throw new \Execution('Session is currently beeing executed');
            }

            $this->queue = $this->clients;

            $active  = null;
            $clients = array();

            $this->mh = curl_multi_init();

            $push_clients = function($init = 0) use (&$clients) {
                for ($i = $init, $cnt = 0; $i < $this->concurrency; ++$i) {
                    if (!($client = array_shift($this->queue))) break;

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

                while (($result = curl_multi_exec($this->mh, $active)) == CURLM_CALL_MULTI_PERFORM);

                if ($result != CURLM_OK) break;

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
            } while($active > 0 || count($this->queue) > 0 || $pushed > 0);

            curl_multi_close($this->mh);
            $this->mh = null;
        }
    }
}
