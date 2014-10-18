<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\riak {
    /**
     * Riak request class.
     *
     * @octdoc      c:riak/request
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class request extends \org\octris\core\net\client\http
    /**/
    {
        /**
         * HTTP status code of last request.
         *
         * @octdoc  p:request/$status
         * @type    int
         */
        protected $status;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:request/__construct
         * @param   \org\octris\core\type\uri       $uri                URI the request is located at.
         */
        public function __construct(\org\octris\core\type\uri $uri, $method = self::T_GET)
        /**/
        {
            parent::__construct($uri, $method);
        }
        
        /**
         * Execute request.
         *
         * @octdoc  m:requext/execute
         * @param   string|array|resource   $body           Optional body to set for POST or PUT request.
         * @param   bool                    $binary         Optional binary transfer mode for POST or PUT request.
         * @return  mixed                                   Returns response body or false if request failed.
         */
        public function execute($body = null, $binary = false)
        /**/
        {
            $result = parent::execute($body, $binary);

            if (($this->getStatus()) == 200) {
                switch ($this->getContentType()) {
                    case 'text/html':
                        $result = trim($result);
                        break;
                    case 'application/json':
                        $result = json_decode($result, true);
                        break;
                    default:
                        $result = null;
                        break;
                }
            } else {
                $result = false;
            }
            
            return $result;
        }
    }
}
