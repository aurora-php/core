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
 * MongoDB database device.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Mongodb extends \Octris\Core\Db\Device
{
    /**
     * Name of database to access.
     *
     * @type    string
     */
    protected $database;

    /**
     * Username to use for connection.
     *
     * @type    string
     */
    protected $username;

    /**
     * Password to use for connection.
     *
     * @type    string
     */
    protected $password;

    /**
     * Constructor.
     *
     * @param   string          $host               Host of database server.
     * @param   int             $port               Port of database server.
     * @param   string          $database           Name of database.
     * @param   string          $username           Optional username to use for connection.
     * @param   string          $password           Optional password to use for connection.
     */
    public function __construct($host, $port, $database, $username = '', $password = '')
    {
        parent::__construct();

        $this->addHost(\Octris\Core\Db::T_DB_MASTER, array(
            'host'     => $host,
            'port'     => $port,
            'database' => ($this->database = $database),
            'username' => ($this->username = $username),
            'password' => ($this->password = $password),
        ));
    }

    /**
     * Add slave database connection.
     *
     * @param   string          $host               Host of database server.
     * @param   int             $port               Port of database server.
     * @param   string          $database           Optional name of database of slave.
     * @param   string          $username           Optional username to use for connection.
     * @param   string          $password           Optional password to use for connection.
     */
    public function addSlave($host, $port, $database = null, $username = null, $password = null)
    {
        $this->addHost(\Octris\Core\Db::T_DB_SLAVE, array(
            'host'     => $host,
            'port'     => $port,
            'database' => (is_null($database) ? $this->database : $database),
            'username' => (is_null($username) ? $this->username : $username),
            'password' => (is_null($password) ? $this->password : $password) ,
        ));
    }

    /**
     * Create database connection.
     *
     * @param   array                       $options        Host configuration options.
     * @return  \Octris\Core\Db\Device\IConnection     Connection to a database.
     */
    protected function createConnection(array $options)
    {
        $cn = new \Octris\Core\Db\Device\Mongodb\Connection($this, $options);

        return $cn;
    }
}
