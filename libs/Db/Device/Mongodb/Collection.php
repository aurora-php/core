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
 * MongoDB database collection.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Collection
{
    /**
     * Device the collection belongs to.
     *
     * @type    \Octris\Core\Db\Device\Mongodb
     */
    protected $device;

    /**
     * Instance of collection.
     *
     * @type    string
     */
    protected $collection;

    /**
     * Constructor.
     *
     * @param   \Octris\Core\Db\Device\Mongodb  $device             Device the connection belongs to.
     * @param   \MongoCollection                    $collection         Instance of collection to handle.
     */
    public function __construct(\Octris\Core\Db\Device\Mongodb $device, \MongoCollection $collection)
    {
        $this->device     = $device;
        $this->collection = $collection;
    }

    /**
     * Return name of collection.
     *
     * @return  string                                              Name of collection.
     */
    public function getName()
    {
        return $this->collection->getName();
    }

    /**
     * Create an empty object for storing data into specified collection.
     *
     * @param   array                                           $data       Optional data to store in data object.
     * @return  \Octris\Core\Db\Device\Mongodb\Dataobject               Data object.
     */
    public function create(array $data = array())
    {
        return new \Octris\Core\Db\Device\Mongodb\Dataobject($this->device, $this->getName(), $data);
    }

    /**
     * Query the database and count the results.
     *
     * @param   array           $query                      Query conditions.
     * @param   int             $offset                     Optional offset to start query result from.
     * @param   int             $limit                      Optional limit of result items.
     * @return  int                                         Number of items found.
     */
    public function count(array $query, $offset = 0, $limit = null)
    {
        return $this->collection->count($query, $offset, $limit);
    }

    /**
     * Create an index in database.
     *
     * @param   array           $keys                       Key(s) to create index for.
     * @param   array           $options                    Optional options for index.
     */
    public function ensureIndex(array $keys, array $options = array())
    {
        $this->collection->ensureIndex($keys, $options);
    }

    /**
     * Fetch the stored item of a specified key.
     *
     * @param   string          $key                                Key (_id) of item to fetch.
     * @return  \Octris\Core\Db\Device\Mongodb\Dataobject|bool  Either a data object containing the found item or false if no item was found.
     */
    public function fetch($key)
    {
        $cursor = $this->query(array('_id' => new \MongoId($key)));

        return ($cursor->next() ? $cursor->current : false);
    }

    /**
     * Query a MongoDB collection and return the first found item.
     *
     * @param   array           $query                              Query conditions.
     * @param   array           $sort                               Optional sorting parameters.
     * @param   array           $fields                             Optional fields to return.
     * @param   array           $hint                               Optional query hint.
     * @return  \Octris\Core\Db\Device\Mongodb\Dataobject|bool  Either a data object containing the found item or false if no item was found.
     */
    public function first(array $query, array $sort = null, array $fields = array(), array $hint = null)
    {
        $cursor = $this->query($query, 0, 1, $sort, $fields, $hint);

        return ($cursor->next() ? $cursor->current : false);
    }

    /**
     * Query a MongoDB collection.
     *
     * @param   array           $query                      Query conditions.
     * @param   int             $offset                     Optional offset to start query result from.
     * @param   int             $limit                      Optional limit of result items.
     * @param   array           $sort                       Optional sorting parameters.
     * @param   array           $fields                     Optional fields to return.
     * @param   array           $hint                       Optional query hint.
     * @return  \Octris\Core\Db\Device\Mongodb\Result   Result object.
     */
    public function query(array $query, $offset = 0, $limit = null, array $sort = null, array $fields = array(), array $hint = null)
    {
        if (($cursor = $this->collection->find($query, $fields)) === false) {
            throw new \Exception('unable to query database');
        } else {
            if (!is_null($sort)) {
                $cursor->sort($sort);
            }
            if ($offset > 0) {
                $cursor->skip($offset);
            }
            if (!is_null($limit)) {
                $cursor->limit($limit);
            }
        }

        return new \Octris\Core\Db\Device\Mongodb\Result(
            $this->device,
            $this->getName(),
            $cursor
        );
    }

    /**
     * Insert an object into a database collection.
     *
     * @param   array           $object                     Data to insert into collection.
     */
    public function insert(array $object)
    {
        return $this->collection->insert($object);
    }

    /**
     * Update data in database collection.
     *
     * @param   array           $criteria                   Search criteria for object(s) to update.
     * @param   array           $object                     Data to update collection with.
     * @param   array           $options                    Optional options.
     */
    public function update(array $criteria, array $object, array $options = null)
    {
        return $this->collection->update($criteria, $object, $options);
    }

    /**
     * Remove data from database.
     *
     * @param   array           $criteria                   Search criteria for object(s) to remove.
     * @param   array           $options                    Optional options.
     */
    public function remove(array $criteria, array $options = array())
    {
        $this->collection->remove($criteria, $options);
    }
}
