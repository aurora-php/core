<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\db\type;

/**
 * Common data object.
 *
 * @octdoc      c:type/dataobject
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class dataobject extends \octris\core\db\type\subobject implements \JsonSerializable
{
    /**
     * Instance of database device responsable for connections.
     *
     * @octdoc  p:dataobject/$device
     * @type    \octris\core\db\device
     */
    protected $device;
    /**/

    /**
     * Name of collection the dataobject has access to.
     *
     * @octdoc  p:dataobject/$collection
     * @type    string
     */
    protected $collection;
    /**/

    /**
     * Object ID -- uniq key that is used for storing the object in the database.
     *
     * @octdoc  p:dataobject/$_id
     * @type    string
     */
    protected $_id = null;
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:dataobject/__construct
     * @param   \octris\core\db\device      $device         Device the connection belongs to.
     * @param   string                          $collection     Name of collection the dataobject belongs to.
     * @param   array                           $data           Data to initialize dataobject with,
     */
    public function __construct(\octris\core\db\device $device, $collection, array $data = array())
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
     *
     * @octdoc  m:dataobject/__clone
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
     * @octdoc  m:dataobject/merge
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
     * @octdoc  m:dataobject/save
     * @param   string              $new_key        Force inserting with the specified key. The method will fall back to an update,
     *                                              if the specified key and the object internal key are identically.
     * @return  bool                                Returns true on success otherwise false.
     */
    public function save($new_key = null)
    {
        $return = true;

        $cn = $this->device->getConnection(\octris\core\db::T_DB_MASTER);
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
     * @octdoc  m:dataobject/offsetGet
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
     * @octdoc  m:dataobject/offsetSet
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
     * @octdoc  m:dataobject/offsetUnset
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
     * @octdoc  a:dataobject/castPhpToDb
     * @param   mixed               $value              Value to cast.
     * @param   string              $name               Name of the value in the data structure.
     * @return  mixed                                   Casted value.
     */
    abstract protected function castPhpToDb($value, $name);
    /**/

    /**
     * Cast a DB internal type to PHP type.
     *
     * @octdoc  a:dataobject/castDbToPhp
     * @param   mixed               $value              Value to cast.
     * @param   string              $name               Name of the value in the data structure.
     * @return  mixed                                   Casted value.
     */
    abstract protected function castDbToPhp($value, $name);
    /**/

    /**
     * Recursive data iteration and casting for preparing data for export to database.
     *
     * @octdoc  m:dataobject/export
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
     * @octdoc  m:dataobject/import
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
     * @octdoc  m:dataobject/jsonSerialize
     * @return  array                                   Array representation of object.
     */
    public function jsonSerialize()
    {
        $data = $this->getArrayCopy();

        $this->export($data);

        return $data;
    }
}

