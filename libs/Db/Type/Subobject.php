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
class SubObject implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Data to store in object.
     *
     * @type    array
     */
    protected $data = array();

    /**
     * Reference to dataobject the subobject belongs to.
     *
     * @type    \Octris\Core\Db\Type\DataObject
     */
    protected $dataobject;

    /**
     * Constructor.
     *
     * @param   array                               $data           Data to initialize object with.
     * @param   \Octris\Core\Db\Type\DataObject     $dataobject     DataObject the subobject is part of.
     */
    public function __construct(array $data, \Octris\Core\Db\Type\DataObject $dataobject)
    {
        $this->dataobject = $dataobject;

        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * Supports deep copy cloning.
     */
    public function __clone()
    {
        foreach ($this->data as $key => $value) {
            if ($value instanceof self) {
                $this[$key] = clone($value);
            }
        }
    }

    /**
     * Merge specified data into dataobject. Note, that the method will throw an exception, if the data to
     * merge contains a new object ID.
     *
     * @param   array                                   $data           Data to merge.
     */
    public function merge(array $data)
    {
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * Convert to array.
     *
     * @return  array                                   Array representation of object.
     */
    public function getArrayCopy()
    {
        $data = $this->data;

        foreach ($data as $key => $value) {
            if ($value instanceof self) {
                $data[$key] = $value->getArrayCopy();
            }
        }

        return $data;
    }

    /**
     * Return an array of keys stored in object.
     *
     * @return  array                                   Stored keys.
     */
    public function getKeys()
    {
        return array_keys($this->data);
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
        return $this->data[$name];
    }

    /**
     * Set object property.
     *
     * @param   string          $name                   Name of property to set.
     * @param   mixed           $value                  Value to set for property.
     */
    public function offsetSet($name, $value)
    {
        if (is_array($value)) {
            $value = new self($value, $this->dataobject);
        }

        if ($name === null) {
            $this->data[] = $value;
        } else {
            $this->data[$name] = $value;
        }
    }

    /**
     * Unset an object property.
     *
     * @param   string          $name                   Name of property to unset.
     */
    public function offsetUnset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Test if an object property exists.
     *
     * @param   string          $name                   Name of property to test.
     * @return  bool                                    Returns true if a property exists.
     */
    public function offsetExists($name)
    {
        return isset($this->data[$name]);
    }

    /** Countable **/

    /**
     * Returns number of items stored in object.
     *
     * @return  int                                     Number of items stored.
     */
    public function count()
    {
        return count($this->data);
    }

    /** IteratorAggregate **/

    /**
     * Return iterator to iterate over object data.
     *
     * @return  \Octris\Core\Db\Device\Riak\DataIterator        Instance of iterator.
     */
    public function getIterator()
    {
        return new \Octris\Core\Db\Type\RecursiveDataIterator(clone($this));
    }
}
