<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\db;

/**
 * Database devices base class.
 *
 * @octdoc      c:db/device
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class device {
    /**
     * Storage for host configurations.
     *
     * @octdoc  p:device/$hosts
     * @type    array
     */
    protected $hosts = array(
        \octris\core\db::T_DB_MASTER => array(),
        \octris\core\db::T_DB_SLAVE  => array()
    );
    /**/

    /**
     * Active connections.
     *
     * @octdoc  p:device/$connections
     * @type    array
     */
    protected $connections = array();
    /**/

    /**
     * Storage of free database connections.
     *
     * @octdoc  p:device/$pool
     * @type    array
     */
    protected $pool = array(
        \octris\core\db::T_DB_MASTER => array(),
        \octris\core\db::T_DB_SLAVE  => array()
    );
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:device/__construct
     */
    public function __construct()
    {
    }

    /**
     * Add host configuration of specified type.
     *
     * @octdoc  m:device/addHost
     * @param   string                      $type               Type of host to add (master / slave).
     * @param   array                       $options            Host configuration options for database master.
     * @param   bool                        $master_as_slave    Whether to add a master connection as slave, too.
     */
    protected function addHost($type, array $options, $master_as_slave = true)
    {
        $this->hosts[$type][] = $options;

        if ($type == \octris\core\db::T_DB_MASTER && $master_as_slave) {
            $this->hosts[\octris\core\db::T_DB_SLAVE][] = $options;
        }
    }

    /**
     * Create a new database connection for specified configuration options.
     *
     * @octdoc  m:device/createConnection
     * @param   array                       $options        Host configuration options.
     * @return  \octris\core\db\device\onnection_if     Connection to a database.
     */
    abstract protected function createConnection(array $options);
    /**/

    /**
     * Return a database connection of specified type.
     *
     * @octdoc  m:device/getConnection
     * @param   string                      $type           Optional type of connection.
     * @return  \octris\core\db\device\onnection_if     Connection to a database.
     */
    public function getConnection($type = \octris\core\db::T_DB_MASTER)
    {
        if ($type != \octris\core\db::T_DB_MASTER && $type != \octris\core\db::T_DB_SLAVE) {
            throw new \Exception('unknown connection type "' . $type . '"');
        } else {
            if (!($cn = array_shift($this->pool[$type]))) {
                // no more connections in the pool, create new one
                if (count($this->hosts[$type]) == 0) {
                    throw new \Exception(sprintf('No database configuration available for connection to a "%s" host.', $type));
                }

                shuffle($this->hosts[$type]);

                $cn = $this->createConnection($this->hosts[$type][0]);

                if (!($cn instanceof \octris\core\db\device\connection_if)) {
                    throw new \Exception('connection handler needs to implement interface "\octris\core\db\device\connection_if"');
                }
            }

            $this->connections[spl_object_hash($cn)] = $type;
        }

        return $cn;
    }

    /**
     * Release a connection, push it back into the pool.
     *
     * @octdoc  m:pool/releaseConnection
     * @param   \octris\core\db\device\connection_if   $cn     Connection to release to pool.
     */
    public function release(\octris\core\db\device\connection_if $cn)
    {
        $hash = spl_object_hash($cn);

        if (!isset($this->connections[$hash])) {
            throw new \Exception('Connection is not handled by this device');
        }

        array_push($this->pool[$this->connections[$hash]], $cn);
        unset($this->connections[$hash]);
    }
}

