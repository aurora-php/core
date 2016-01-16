<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Type\Collection;

/**
 * Base collection type.
 *
 * @copyright   copyright (c) 2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class Abstractcollection implements \Iterator, \ArrayAccess, \Serializable, \JsonSerializable, \Countable
{
    /**
     * Data of collection.
     *
     * @type    array
     */
    protected $data = array();

    /**
     * Position for iterator.
     *
     * @type    int
     */
    protected $position = 0;

    /**
     * Constructor.
     *
     * @param   mixed       $data               Optional data to initialize collection with.
     */
    public function __construct($data = array())
    {
        $this->data = $data;
    }

    /**
     * Return stored data if var_dump is used with collection.
     *
     * @return  array                           Stored data.
     */
    public function __debugInfo()
    {
        return $this->data;
    }

    /**
     * Return contents of collection as array.
     *
     * @return  array                                   Contents of collection.
     */
    public function getArrayCopy()
    {
        return $this->data;
    }

    /** Iterator **/

    /**
     * Return key of item.
     *
     * @return  string                                      Key of item.
     */
    public function key() {
        return key($this->data);
    }

    /**
     * Return value of item.
     *
     * @return  scalar                                      Value of item.
     */
    public function current() {
        return current($this->data);
    }

    /**
     * Move pointer to the next item but skip sections.
     */
    public function next()
    {
        do {
            $item = next($this->data);
            ++$this->position;
        } while (is_array($item));
    }

    /**
     * Rewind collection.
     */
    public function rewind()
    {
        reset($this->data);
        $this->position = 0;
    }

    /**
     * Test if position is valid.
     */
    public function valid()
    {
        return (count($this->data) > $this->position);
    }

    /** ArrayAccess **/

    /**
     * Get value from collection. Allows access by dot-notation.
     *
     * @param   string      $offs       Offset to get value from.
     * @return  mixed                   Value stored at offset.
     */
    public function offsetGet($offs)
    {
        if (strpos($offs, '.') !== false) {
            $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
            $ret   =& $this->data;

            for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                $ret =& $ret[$parts[$i]];
            }
        } else {
            $ret =& $this->data[$offs];
        }

        return $ret;
    }

    /**
     * Set value in collection at specified offset. Allows access by dot-notation.
     *
     * @param   string      $offs       Offset to set value at.
     * @param   mixed       $value      Value to set at offset.
     */
    public function offsetSet($offs, $value)
    {
        if (is_null($offs)) {
            // $...[] =
            $this->data[] = $value;
        } elseif (strpos($offs, '.') !== false) {
            $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
            $ret   =& $this->data;

            for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                if (!array_key_exists($parts[$i], $ret)) {
                    $ret[$parts[$i]] = array();
                }

                $ret =& $ret[$parts[$i]];
            }

            $ret = $value;
        } else {
            $this->data[$offs] = $value;
        }
    }

    /**
     * Check whether the offset exists in collection. Allows access by dot-notation.
     *
     * @return  bool                                            Returns true, if offset exists.
     */
    public function offsetExists($offs)
    {
        if (strpos($offs, '.') !== false) {
            $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
            $ret   =& $this->data;

            for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                if (!($return = array_key_exists($parts[$i], $ret))) {
                    break;
                }

                unset($ret[$parts[$i]]);
            }
        } else {
            $return = isset($this->data[$offs]);
        }

        return $return;
    }

    /**
     * Unset data in collection at specified offset. Allows access by dot-notation.
     *
     * @param   string      $offs       Offset to unset.
     */
    public function offsetUnset($offs)
    {
        if (strpos($offs, '.') !== false) {
            $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
            $ret   =& $this->data;

            for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                if (!($return = array_key_exists($parts[$i], $ret))) {
                    break;
                }

                $ret =& $ret[$parts[$i]];
            }
        } else {
            unset($this->data[$offs]);
        }
    }

    /** Serializable **/

    /**
     * Get's called when something wants to serialize the collection.
     *
     * @return  string                      Serialized content of collection.
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * Get's called when something wants to unserialize the collection.
     *
     * @param   string                      Data to unserialize as collection.
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }

    /** JsonSerializable **/

    /**
     * Get's called when something wants to json-serialize the collection.
     *
     * @return  string                      Json-serialized content of collection.
     */
    public function jsonSerialize()
    {
        return json_encode($this->data);
    }

    /** Countable **/

    /**
     * Return number of items in collection.
     *
     * @return  int                         Number of items.
     */
    public function count()
    {
        return count($this->data);
    }
}
