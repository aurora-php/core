<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Device\Sqlite;

/**
 * SQLite connection handler.
 *
 * @copyright   copyright (c) 2012-2013 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Connection extends \SQLite3 implements \Octris\Core\Db\Device\IConnection
{
    /**
     * Device the connection belongs to.
     *
     * @type    \octris\core\db\device\sqlite
     */
    protected $device;
    
    /**
     * Constructor.
     *
     * @param   \Octris\Core\Db\Device\Sqlite   $device             Device the connection belongs to.
     * @param   array                               $options            Connection options.
     */
    public function __construct(\Octris\Core\Db\Device\Sqlite $device, array $options)
    {
        $this->device = $device;

        parent::__construct($options['file'], $options['flags'], $options['key']);
    }

    /**
     * Release connection.
     *
     */
    public function release()
    {
        $this->device->release($this);
    }

    /**
     * Check connection.
     *
     * @return  bool                                            Returns true if the connection is alive.
     */
    public function isAlive()
    {
        return true;
    }

    /**
     * Resolve a database reference.
     *
     * @todo    Implementation.
     * @param   \Octris\Core\Db\Type\Dbref                          $dbref      Database reference to resolve.
     * @return  \octris\core\db\device\sqlite\dataobject|bool                   Data object or false if reference could not he resolved.
     */
    public function resolve(\Octris\Core\Db\Type\Dbref $dbref)
    {
        return false;
    }

    /**
     * Return list of collections.
     *
     * @return  array|bool                                      Array of names of collections or false in case
     *                                                          of an error.
     */
    public function getCollections()
    {
        $sql = 'SELECT  *
                FROM    sqlite_master
                WHERE   type="table"';

        $result = $this->query($sql);
        $return = array();

        foreach ($result as $row) {
            $return[] = $row['name'];
        }

        return $return;
    }

    /**
     * Return instance of collection object.
     *
     * @param   string          $name                               Name of collection to return instance of.
     * @return  \octris\core\db\device\sqlite\collection        Instance of sqlte collection.
     */
    public function getCollection($name)
    {
        return new \Octris\Core\Db\Device\Sqlite\Collection(
            $this->device,
            $this,
            $name
        );
    }

    /**
     * Initialize prepared statement.
     *
     * @param   string                      $sql                SQL statement to use as prepared statement.
     * @return  \octris\core\db\sqlite\statement            Instance of prepared statement.
     */
    public function prepare($sql)
    {
        $stmt = parent::prepare($sql);

        return new \Octris\Core\Db\Device\Sqlite\Statement($this->device, $stmt);
    }

    /**
     * Execute a SQL query.
     *
     * @param   string                      $sql                SQL statement to execute.
     * @return  \octris\core\db\sqlite\result               Instance of result class.
     */
    public function query($sql)
    {
        $result = parent::query($sql);

        return new \Octris\Core\Db\Device\Sqlite\Result($this->device, $result);
    }
}
