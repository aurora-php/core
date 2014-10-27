<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Device\Riak;

use \Octris\Core\Net\Client\Http as http;

/**
 * Riak database connection.
 *
 * @octdoc      c:riak/connection
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Connection implements \Octris\Core\Db\Device\Connection_if
{
    /**
     * Device the connection belongs to.
     *
     * @octdoc  p:connection/$device
     * @type    \octris\core\db\device\riak
     */
    protected $device;
    /**/

    /**
     * URI instance.
     *
     * @octdoc  p:connection/$uri
     * @type    \octris\core\type\uri
     */
    protected $uri;
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:connection/__construct
     * @param   \Octris\Core\Db\Device\Riak     $device             Device the connection belongs to.
     * @param   array                               $options            Connection options.
     */
    public function __construct(\Octris\Core\Db\Device\Riak $device, array $options)
    {
        $this->device = $device;

        $this->uri = \Octris\Core\Type\Uri::create(
            $options['host'], $options['port']
        );
    }

    /**
     * Release connection.
     *
     * @octdoc  m:connection/release
     */
    public function release()
    {
        $this->device->release($this);
    }

    /**
     * Return instance of request class.
     *
     * @octdoc  m:connection/getRequest
     * @param   string                  $path                   Path of request to return.
     * @param   array                   $args                   Optional request parameters.
     * @return  \octris\core\db\riak\request                Request object.
     */
    public function getRequest($method, $path = '/', array $args = null)
    {
        $uri = clone($this->uri);
        $uri->path  = '/' . ltrim($path, '/');

        if (is_array($args)) {
            $uri->query = $args;
        }

        return new \Octris\Core\Db\Device\Riak\Request($uri, $method);
    }

    /**
     * Check connection.
     *
     * @octdoc  m:connection/isAlive
     * @return  bool                                            Returns true if the connection is alive.
     */
    public function isAlive()
    {
        $result = $this->getRequest(http::T_GET, '/ping')->execute();

        return ($result == 'OK');
    }

    /**
     * Resolve a database reference.
     *
     * @octdoc  m:connection_if/resolve
     * @param   \Octris\Core\Db\Type\Dbref                          $dbref      Database reference to resolve.
     * @return  \octris\core\db\device\riak\dataobject|bool                     Data object or false if reference could not he resolved.
     *
     * @todo
     */
    public function resolve(\Octris\Core\Db\Type\Dbref $dbref)
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
     * @return  \octris\core\db\device\riak\collection          Instance of riak collection.
     */
    public function getCollection($name)
    {
        if (!is_string($name)) {
            throw new \Exception('name must be of type string');
        }

        return new \Octris\Core\Db\Device\Riak\Collection(
            $this->device,
            $this,
            $name
        );
    }
}
