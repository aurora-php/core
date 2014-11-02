<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Device\Mongodb;

/**
 * MongoDB database connection.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Connection implements \Octris\Core\Db\Device\IConnection
{
    /**
     * Device the connection belongs to.
     *
     * @type    \Octris\Core\Db\Device\Mongodb
     */
    protected $device;

    /**
     * Instance of mongo class.
     *
     * @type    \Mongo
     */
    protected $mongo;

    /**
     * Connection to a database.
     *
     * @type    \MongoDB
     */
    protected $db;

    /**
     * Constructor.
     *
     * @param   \Octris\Core\Db\Device\Mongodb  $device             Device the connection belongs to.
     * @param   array                               $options            Connection options.
     */
    public function __construct(\Octris\Core\Db\Device\Mongodb $device, array $options)
    {
        $class = (class_exists('\MongoClient')
                    ? '\MongoClient'
                    : '\Mongo');

        $this->device = $device;
        $this->mongo  = new $class(
            'mongodb://' . $options['host'] . ':' . $options['port'],
            array(
                // 'username' => $options['username'],
                // 'password' => $options['password'],
                'db'       => $options['database']
            )
        );

        $this->db = $this->mongo->selectDB($options['database']);
    }

    /**
     * Release connection.
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
     * @param   \Octris\Core\Db\Type\DbRef                          $dbref      Database reference to resolve.
     * @return  \Octris\Core\Db\Device\Mongodb\DataObject|bool                  Data object or false if reference could not he resolved.
     */
    public function resolve(\Octris\Core\Db\Type\DbRef $dbref)
    {
        $cl = $this->db->selectCollection($collection);

        $data = $cl->getDBRef(\MongoDBRef::create(
            $dbref->collection,
            $dbref->key
        ));

        $return = new \Octris\Core\Db\Device\Mongodb\DataObject($this->device, $collection, $data);

        return $return;
    }

    /**
     * Execute a database command.
     *
     * @param   array           $command                    Command to execute in database.
     * @param   array           $options                    Optional options for command.
     * @return  mixed                                       Return value of executed command.
     */
    public function command(array $command, array $options = array())
    {
        return $this->db->command($command, $options);
    }

    /**
     * Execute javascript code on the database server.
     *
     * @param   string          $code                       Code to execute on  the server.
     * @param   array           $args                       Additional optional arguments.
     * @return  mixed                                       Return value of the executed code.
     */
    public function execute($code, array $args = array())
    {
        return $this->db->execute($code, $args);
    }

    /**
     * Return instance of collection object.
     *
     * @param   string          $name                               Name of collection to return instance of.
     * @return  \Octris\Core\Db\Device\Mongodb\Collection       Instance of a mongodb collection.
     */
    public function getCollection($name)
    {
        return new \Octris\Core\Db\Device\Mongodb\Collection(
            $this->device,
            $this->db->selectCollection($name)
        );
    }
}
