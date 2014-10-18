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
    use \org\octris\core\net\client\http as http;
    
    /**
     * Riak database connection.
     *
     * @octdoc      c:riak/connection
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class connection implements \org\octris\core\db\device\connection_if
    /**/
    {
        /**
         * Device the connection belongs to.
         *
         * @octdoc  p:connection/$device
         * @type    \org\octris\core\db\device\riak
         */
        protected $device;
        /**/

        /**
         * URI instance.
         *
         * @octdoc  p:connection/$uri
         * @type    \org\octris\core\type\uri
         */
        protected $uri;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:connection/__construct
         * @param   \org\octris\core\db\device\riak     $device             Device the connection belongs to.
         * @param   array                               $options            Connection options.
         */
        public function __construct(\org\octris\core\db\device\riak $device, array $options)
        /**/
        {
            $this->device = $device;
            
            $this->uri = \org\octris\core\type\uri::create(
                $options['host'], $options['port']
            );
        }

        /**
         * Release connection.
         *
         * @octdoc  m:connection/release
         */
        public function release()
        /**/
        {
            $this->device->release($this);
        }

        /**
         * Return instance of request class.
         *
         * @octdoc  m:connection/getRequest
         * @param   string                  $path                   Path of request to return.
         * @param   array                   $args                   Optional request parameters.
         * @return  \org\octris\core\db\riak\request                Request object.
         */
        public function getRequest($method, $path = '/', array $args = null)
        /**/
        {
            $uri = clone($this->uri);
            $uri->path  = '/' . ltrim($path, '/');
            
            if (is_array($args)) {
                $uri->query = $args;                
            }
            
            return new \org\octris\core\db\device\riak\request($uri, $method);
        }

        /**
         * Check connection.
         *
         * @octdoc  m:connection/isAlive
         * @return  bool                                            Returns true if the connection is alive.
         */
        public function isAlive()
        /**/
        {
            $result = $this->getRequest(http::T_GET, '/ping')->execute();
            
            return ($result == 'OK');
        }

        /**
         * Resolve a database reference.
         *
         * @octdoc  m:connection_if/resolve
         * @param   \org\octris\core\db\type\dbref                          $dbref      Database reference to resolve.
         * @return  \org\octris\core\db\device\riak\dataobject|bool                     Data object or false if reference could not he resolved.
         *
         * @todo
         */
        public function resolve(\org\octris\core\db\type\dbref $dbref)
        /**/
        {
            return false;
        }

        /**
         * Return list of collections.
         *
         * @octdoc  m:connection/getCollections
         * @return  array|bool                                      Array of names of collections or false in case
         *                                                          of an error.
         */
        public function getCollections()
        /**/
        {
            $result = $this->getRequest(http::T_GET, '/buckets?buckets=true')->execute();
            
            return ($result !== false
                    ? $result['buckets']
                    : $result);
        }

        /**
         * Return instance of collection object.
         *
         * @octdoc  m:connection/getCollection
         * @param   string          $name                               Name of collection to return instance of.
         * @return  \org\octris\core\db\device\riak\collection          Instance of riak collection.
         */
        public function getCollection($name)
        /**/
        {
            if (!is_string($name)) {
                throw new \Exception('name must be of type string');
            }
            
            return new \org\octris\core\db\device\riak\collection(
                $this->device,
                $this,
                $name
            );
        }
    }
}
