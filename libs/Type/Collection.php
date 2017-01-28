<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Type;

require_once(__DIR__ . '/Collection/functions.php');

/**
 * Collection type. Implements special access on array objects.
 *
 * @copyright   copyright (c) 2010-2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Collection implements \Iterator, \ArrayAccess, \Serializable, \JsonSerializable, \Countable
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
        if (($tmp = Collection\Normalize($data)) === false) {
            // not an array
            throw new \Exception('don\'t know how to handle parameter of type "' . gettype($data) . '"');
        }

        $this->data = $tmp;
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
     * Exchange the data array for another one.
     *
     * @param   mixed       $value      The new array or object to exchange to current data with.
     * @return  array                   Data stored in collection
     */
    public function exchangeArray($value)
    {
        if (($tmp = Collection\Normalize($value)) === false) {
            // not an array
            throw new \Exception('Unable to handle parameter of type "' . gettype($tmp) . '".');
        } else {
            $return = $this->data;
            $this->data = $tmp;
        }

        return $return;
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

    /** Sorting **/

    /**
     * Sort the entries by value.
     *
     * @param   Collator    $collator       Optional collator to use for comparision.
     */
    public function asort(\Collator $collator = null)
    {
        $collator = $collator ?: new \Collator(\Octris\Core\L10n::getInstance()->getLocale());

        uasort($this->data, function ($string1, $string2) use ($collator) {
            return \Octris\Core\Type\Text::strcmp($string1, $string2, $collator);
        });
    }

    /**
     * Sort the entries by key.
     *
     * @param   Collator    $collator       Optional collator to use for comparision.
     */
    public function ksort(\Collator $collator = null)
    {
        $collator = $collator ?: new \Collator(\Octris\Core\L10n::getInstance()->getLocale());

        uksort($this->data, function ($string1, $string2) use ($collator) {
            return \Octris\Core\Type\Text::strcmp($string1, $string2, $collator);
        });
    }

    /**
     * Sort the entries with a user-defined comparison function and maintain key association.
     *
     * @param   callable    $callback                   The callback comparision function.
     */
    public function uasort(callable $callback)
    {
        uasort($this->data, $callback);
    }

    /**
     * Sort the entries by keys using a user-defined comparison function.
     *
     * @param   callable    $callback                   The callback comparison function.
     */
    public function uksort(callable $callback)
    {
        uksort($this->data, $callback);
    }

    /**
     * Sort an array using a case insensitive "natural order" algorithm.
     *
     * @param   Collator    $collator       Optional collator to use for comparision.
     */
    public function natcasesort(\Collator $collator = null)
    {
        $collator = $collator ?: new \Collator(\Octris\Core\L10n::getInstance()->getLocale());

        uasort($this->data, function ($string1, $string2) use ($collator) {
            return \Octris\Core\Type\Text::strnatcasecmp($string1, $string2, $collator);
        });
    }

    /**
     * Sort entries using a "natural order" algorithm.
     *
     * @param   Collator    $collator       Optional collator to use for comparision.
     */
    public function natsort(\Collator $collator = null)
    {
        $collator = $collator ?: new \Collator(\Octris\Core\L10n::getInstance()->getLocale());

        uasort($this->data, function ($string1, $string2) use ($collator) {
            return \Octris\Core\Type\Text::strnatcmp($string1, $string2, $collator);
        });
    }

    /** Iterator **/

    /**
     * Return key of item.
     *
     * @return  string                                      Key of item.
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Return value of item.
     *
     * @return  mixed                                       Value of item.
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Move pointer to the next item but skip sections.
     *
     * @return  mixed                                       Value of item.
     */
    public function next()
    {
        $item = next($this->data);
        ++$this->position;

        return $item;
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
     * @return  mixed                   Value stored at offset, arrays are returned as Subcollection.
     */
    public function offsetGet($offs)
    {
        if (strpos($offs, '.') !== false) {
            $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
            $ret   =& $this->data;

            for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                if (!array_key_exists($parts[$i], $ret)) {
                    trigger_error('Undefined index "' . $parts[$i] . '" in "' . $offs . '".');

                    return null;
                } else {
                    $ret =& $ret[$parts[$i]];
                }
            }
        } else {
            if (!array_key_exists($offs, $this->data)) {
                trigger_error('Undefined index "' . $offs . '".');

                return null;
            }

            $ret =& $this->data[$offs];
        }

        return (is_array($ret)
                ? new \Octris\Core\Type\Collection\Subcollection($ret)
                : $ret);
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
