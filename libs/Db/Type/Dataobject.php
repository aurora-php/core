<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Type;

/**
 * Common data object.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class Dataobject extends \Octris\Core\Db\Type\Subobject implements \JsonSerializable
{
    /**
     * Instance of database device responsable for connections.
     *
     * @type    \Octris\Core\Db\Device
     */
    protected $device;

    /**
     * Name of collection the dataobject has access to.
     *
     * @type    string
     */
    protected $collection;

    /**
     * Object ID -- uniq key that is used for storing the object in the database.
     *
     * @type    string
     */
    protected $_id = null;

    /**
     * Constructor.
     *
     * @param   \Octris\Core\Db\Device      $device         Device the connection belongs to.
     * @param   string                          $collection     Name of collection the dataobject belongs to.
     * @param   array                           $data           Data to initialize dataobject with,
     */
    public function __construct(\Octris\Core\Db\Device $device, $collection, array $data = array())
    {
        $this->device     = $device;
        $this->collection = $collection;

        $this->import($data);

        if (array_key_exists('_id', $data)) {
            $this->_id = (empty($data['_id'])
                            ? null
                            : (string)$data['_id']);

            unset($data['_id']);
        }

        parent::__construct($data, $this);
    }

    /**
     * Make sure that object Id get's reset, when object is cloned, because no duplicate Ids
     * are allowed for objects in a collection.
     */
    public function __clone()
    {
        $this->_id = null;

        parent::__clone();
    }

    /**
     * Merge specified data into dataobject. Note, that the method will throw an exception, if the data to
     * merge contains a new object ID.
     *
     * @param   array                                   $data           Data to merge.
     */
    public function merge(array $data)
    {
        if (array_key_exists('_id', $data)) {
            throw new \Exception('Property "_id" is read-only');
        } else {
            $this->import($data);

            parent::merge($data);
        }
    }

    /**
     * Save dataobject to bucket.
     *
     * @param   string              $new_key        Force inserting with the specified key. The method will fall back to an update,
     *                                              if the specified key and the object internal key are identically.
     * @return  bool                                Returns true on success otherwise false.
     */
    public function save($new_key = null)
    {
        $return = true;

        $cn = $this->device->getConnection(\Octris\Core\Db::T_DB_MASTER);
        $cl = $cn->getCollection($this->collection);

        if (is_null($this->_id) || (!is_null($new_key) && $this->_id !== $new_key)) {
            // insert new object
            if (($return = !!($new_key = $cl->insert($this, $new_key)))) {
                $this->_id = $new_key;
            }
        } else {
            // update object
            $return = $cl->update($this, $this->_id);
        }

        $cn->release();

        return $return;
    }

    /** ArrayAccess **/

    /**
     * Get object property.
     *
     * @param   string          $name                   Name of property to get.
     * @return  mixed                                   Data stored in property.
     */
    public function offsetGet($name)
    {
        return ($name == '_id'
                ? $this->_id
                : parent::offsetGet($name));
    }

    /**
     * Set object property.
     *
     * @param   string          $name                   Name of property to set.
     * @param   mixed           $value                  Value to set for property.
     */
    public function offsetSet($name, $value)
    {
        if ($name == '_id') {
            throw new \Exception('Property "_id" is read-only');
        } elseif ($name === null) {
            throw new \Exception('Property name cannot be null');
        } else {
            parent::offsetSet($name, $value);
        }
    }

    /**
     * Unset an object property.
     *
     * @param   string          $name                   Name of property to unset.
     */
    public function offsetUnset($name)
    {
        if ($name == '_id') {
            throw new \Exception('property "_id" is read-only');
        } else {
            parent::offsetUnset($name);
        }
    }

    /** Type casting **/

    /**
     * Cast a PHP type to DB internal type.
     *
     * @param   mixed               $value              Value to cast.
     * @param   string              $name               Name of the value in the data structure.
     * @return  mixed                                   Casted value.
     */
    abstract protected function castPhpToDb($value, $name);

    /**
     * Cast a DB internal type to PHP type.
     *
     * @param   mixed               $value              Value to cast.
     * @param   string              $name               Name of the value in the data structure.
     * @return  mixed                                   Casted value.
     */
    abstract protected function castDbToPhp($value, $name);

    /**
     * Recursive data iteration and casting for preparing data for export to database.
     *
     * @param   array               $data               Data to process.
     */
    protected function export(array &$data)
    {
        array_walk_recursive($data, function (&$value, $name) {
            $value = $this->castPhpToDb($value, $name);
        });
    }

    /**
     * Recursive data iteration and casting for preparing data for import into dataobject.
     *
     * @param   array               $data               Data to process.
     */
    protected function import(array &$data)
    {
        array_walk_recursive($data, function (&$value, $name) {
            $value = $this->castDbToPhp($value, $name);
        });
    }

    /** Helper methods for serialization **/

    /**
     * Magic method gets called, when 'json_encode' is used on the object instance.
     *
     * @return  array                                   Array representation of object.
     */
    public function jsonSerialize()
    {
        $data = $this->getArrayCopy();

        $this->export($data);

        return $data;
    }
}
