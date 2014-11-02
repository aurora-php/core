<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Device\Pdo;

/**
 * PDO connection handler.
 *
 * @copyright   copyright (c) 2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Connection implements \Octris\Core\Db\Device\IConnection
{
    /**
     * Device the connection belongs to.
     *
     * @type    \Octris\Core\Db\Device\Pdo
     */
    protected $device;

    /**
     * Instance of PDO class.
     *
     * @type    \PDO
     */
    protected $pdo;

    /**
     * Constructor.
     *
     * @param   \Octris\Core\Db\Device\Pdo  $device             Device the connection belongs to.
     * @param   array                           $options            Connection options.
     */
    public function __construct(\Octris\Core\Db\Device\Pdo $device, array $options)
    {
        $this->pdo = new \PDO($options['dsn'], $options['username'], $options['password'], $options['options']);
    }

    /**
     * Release a connection.
     */
    public function release()
    {
        //$this->pdo->release();
    }

    /**
     * Check availability of a connection.
     *
     * @return  bool                                        Returns true if connection is alive.
     * @todo    Implement driver specific check.
     */
    public function isAlive()
    {
        return true;
    }

    /**
     * Resolve a database reference.
     *
     * @param   \Octris\Core\Db\Type\DbRef                          $dbref      Database reference to resolve.
     * @return  bool                                                                Returns false always due to missing implementagtion.
     * @todo    Add implementation.
     */
    public function resolve(\Octris\Core\Db\Type\DbRef $dbref)
    {
        return false;
    }

    /**
     * Query the database.
     *
     * @param   string              $statement            SQL statement to perform.
     * @param   mixed               ...$params            Optional additional options.
     * @return  \Octris\Core\Db\Pdo\Result            Query result.
     */
    public function query($statement, ...$params)
    {
        if (($res = $this->pdo->query($statement, ...$params)) === false) {
            throw new \Exception($this->errorInfo()[2], $this->errorCode());
        }

        return new \Octris\Core\Db\Device\Pdo\Result($res);
    }

    /**
     * Initialize prepared statement.
     *
     * @param   string              $statement            SQL statement to prepare.
     * @param   array               $options              Optional additional driver options.
     * @return  \Octris\Core\Db\Pdo\Statement         Instance of a prepared statement.
     */
    public function prepare($statement, array $options = array())
    {
        if (($stmt = $this->pdo->prepare($statement, $options)) === false) {
            throw new \Exception('PDO prepare');
        }

        return new \Octris\Core\Db\Device\Pdo\Statement($stmt);
    }

    /**
     * Return instance of collection object.
     *
     * @param   string          $name                               Name of collection to return instance of.
     * @return  \Octris\Core\Db\Device\Pdo\Collection           Instance of a PDO collection.
     * @todo    Add implementation.
     */
    public function getCollection($name)
    {
        // return new \Octris\Core\Db\Device\Pdo\Collection(
        //     $this->device,
        //     $name
        // );
    }
}
