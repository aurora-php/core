<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Device;

/**
 * Riak database device.
 *
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Riak extends \Octris\Core\Db\Device
{
    /**
     * Constructor.
     *
     * @param   string          $host               Host of database server.
     * @param   int             $port               Port of database server.
     */
    public function __construct($host, $port)
    {
        parent::__construct();

        $this->addHost(\Octris\Core\Db::T_DB_MASTER, array(
            'host'     => $host,
            'port'     => $port
        ));
    }

    /**
     * Add database node connection.
     *
     * @param   string          $host               Host of database server.
     * @param   int             $port               Port of database server.
     */
    public function addNode($host, $port)
    {
        $this->addHost(\Octris\Core\Db::T_DB_SLAVE, array(
            'host'     => $host,
            'port'     => $port
        ));
    }

    /**
     * Create database connection.
     *
     * @param   array                       $options        Host configuration options.
     * @return  \octris\core\db\device\onnection_if     Connection to a database.
     */
    protected function createConnection(array $options)
    {
        $cn = new \Octris\Core\Db\Device\Riak\Connection($this, $options);

        return $cn;
    }
}
