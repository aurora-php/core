<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\type {
    /**
     * Collection type. Implements special access on array objects.
     *
     * @octdoc      c:type/collection
     * @copyright   copyright (c) 2010-2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class collection implements \IteratorAggregate, \ArrayAccess, \Serializable, \JsonSerializable, \Countable
    /**/
    {
        /**
         * Stores collection data.
         *
         * @octdoc  p:collection/$storage
         * @type    array
         */
        private $storage = array();
        /**/

        /**
         * Key storage.
         *
         * @octdoc  p:collection/$keys
         * @type    array
         */
        private $keys = array();
        /**/

        /**
         * Iterator class to use for iterating.
         *
         * @octdoc  p:collection/$iterator_class
         * @type    string
         */
        private $iterator_class;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:collection/__construct
         * @param   mixed       $value              Optional value to initialize collection with.
         * @param   string      $iterator_class     Optional name of an iterator class to use instead of default iterator class.
         */
        public function __construct($value = array(), $iterator_class = '\org\octris\core\type\iterator')
        /**/
        {
            if (($tmp = static::normalize($value)) === false) {
                // not an array
                throw new \Exception('don\'t know how to handle parameter of type "' . gettype($value) . '"');
            }

            $this->storage        = $tmp;
            $this->keys           = array_keys($tmp);
            $this->iterator_class = $iterator_class;
        }

        /**
         * Return stored data if var_dump is used with collection.
         *
         * @octdoc  m:collection/__debugInfo
         * @return  array                           Stored data.
         */
        public function __debugInfo()
        /**/
        {
            return $this->storage;
        }

        /**
         * Return iterator for collection.
         *
         * @octdoc  m:collection/getIterator
         * @return  \Iterator                       Iterator instance for iterating over collection.
         */
        public function getIterator()
        /**/
        {
            $class = $this->iterator_class;
            
            return (new $class($this));
        }
        
        /**
         * Return class name of 
         *
         * @octdoc  m:collection/getIteratorClass
         * @return  string                                  Name if iterator class currently set.
         */
        public function getIteratorClass()
        /**/
        {
            return $this->iterator_class;
        }

        /**
         * Change iterator class.
         *
         * @octdoc  m:collection/setIteratorClass
         * @param   string      $class                      Name of iterator class to set for collection.
         */
        public function setIteratorClass($class)
        /**/
        {
            $this->iterator_class = $class;
        }

        /**
         * Return contents of collection as array.
         *
         * @octdoc  m:collection/getArrayCopy
         * @return  array                                   Contents of collection.
         */
        public function getArrayCopy()
        /**/
        {
            return $this->storage;
        }

        /**
         * Append value to collection.
         *
         * @octdoc  m:collection/append
         * @param   mixed       $value                      Value to append to collection.
         */
        public function append($value)
        /**/
        {
            $this->offsetSet(null, $value);
        }

        /** Sorting **/
        
        /**
         * Sort the entries by value.
         *
         * @octdoc  m:collection/asort
         * @param   Collator    $collator       Optional collator to use for comparision.
         */
        public function asort(\Collator $collator = null)
        /**/
        {
            $collator = $collator ?: new \Collator(\org\octris\core\l10n::getInstance()->getLocale());
            
            uasort($this->storage, function($string1, $string2) use ($collator) {
                return \org\octris\core\type\string::strcmp($string1, $string2, $collator);
            });
            
            $this->keys = array_keys($this->storage);
        }
        
        /**
         * Sort the entries by key.
         *
         * @octdoc  m:collection/ksort
         * @param   Collator    $collator       Optional collator to use for comparision.
         */
        public function ksort(\Collator $collator = null)
        /**/
        {
            $collator = $collator ?: new \Collator(\org\octris\core\l10n::getInstance()->getLocale());
            
            uksort($this->storage, function($string1, $string2) use ($collator) {
                return \org\octris\core\type\string::strcmp($string1, $string2, $collator);
            });
            
            $this->keys = array_keys($this->storage);
        }

        /**
         * Sort the entries with a user-defined comparison function and maintain key association.
         *
         * @octdoc  m:collection/uasort
         * @param   callable    $callback                   The callback comparision function.
         */
        public function uasort($callback)
        /**/
        {
            uasort($this->storage, $callback);
            
            $this->keys = array_keys($this->storage);
        }

        /**
         * Sort the entries by keys using a user-defined comparison function.
         *
         * @octdoc  m:collection/uksort
         * @param   callable    $callback                   The callback comparison function.
         */
        public function uksort($callback)
        /**/
        {
            uksort($this->storage, $callback);
            
            $this->keys = array_keys($this->storage);
        }

        /**
         * Sort an array using a case insensitive "natural order" algorithm.
         *
         * @octdoc  m:collection/natcasesort
         * @param   Collator    $collator       Optional collator to use for comparision.
         */
        public function natcasesort(\Collection $collection = null)
        /**/
        {
            $collator = $collator ?: new \Collator(\org\octris\core\l10n::getInstance()->getLocale());
            
            uasort($this->storage, function($string1, $string2) use ($collator) {
                return \org\octris\core\type\string::strnatcasecmp($string1, $string2, $collator);
            });
            
            $this->keys = array_keys($this->storage);
        }
        
        /**
         * Sort entries using a "natural order" algorithm.
         *
         * @octdoc  m:collection/natsort
         * @param   Collator    $collator       Optional collator to use for comparision.
         */
        public function natsort(\Collection $collection = null)
        /**/
        {
            $collator = $collator ?: new \Collator(\org\octris\core\l10n::getInstance()->getLocale());
            
            uasort(function($string1, $string2) use ($collator) {
                return \org\octris\core\type\string::strnatcmp($string1, $string2, $collator);
            });
            
            $this->keys = array_keys($this->storage);
        }

        /** ArrayAccess **/
    
        /**
         * Get value from collection. Allows access by dot-notation.
         *
         * @octdoc  m:collection/offsetGet
         * @param   string      $offs       Offset to get value from.
         */
        public function &offsetGet($offs)
        /**/
        {
            $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
            $ret   =& $this->storage;
            
            for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                $ret =& $ret[$parts[$i]];
            }
    
            return $ret;
        }
    
        /**
         * Set value in collection at specified offset.
         *
         * @octdoc  m:collection/offsetSet
         * @param   string      $offs       Offset to set value at.
         * @param   mixed       $value      Value to set at offset.
         */
        public function offsetSet($offs, $value)
        /**/
        {
            if (is_null($offs)) {
                // $...[] =
                $inc = (int)in_array(0, $this->keys);               // if 0 is already in, we have to increment next index
                $idx = max(array_merge(array(0), $this->keys));     // get next highest numeric index
                $this->keys[]    = $idx + $inc;
                $this->storage[] = $value;
            } else {
                $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
                $ret   =& $this->storage;
            
                for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                    if (!array_key_exists($parts[$i], $ret)) {
                        $ret[$parts[$i]] = array();
                    }

                    $ret =& $ret[$parts[$i]];
                }
                
                $ret = $value;
            }
        }

        /**
         * Check whether the offset exists in collection.
         *
         * @octdoc  m:collection/offsetExists
         * @return  bool                                            Returns true, if offset exists.
         */
        public function offsetExists($offs)
        /**/
        {
            return isset($this->storage[$offs]);
        }

        /**
         * Unset data in collection at specified offset.
         *
         * @octdoc  m:collection/offsetUnset
         * @param   string      $offs       Offset to unset.
         */
        public function offsetUnset($offs)
        /**/
        {
            if (($idx = array_search($offs, $this->keys)) !== false) {
                unset($this->keys[$idx]);
            }
            
            unset($this->storage[$offs]);
        }

        /** Serializable **/
     
        /**
         * Get's called when something wants to serialize the collection.
         *
         * @octdoc  m:collection/serialize
         * @return  string                      Serialized content of collection.
         */
        public function serialize()
        /**/
        {
            return serialize($this->storage);
        }
        
        /**
         * Get's called when something wants to unserialize the collection.
         *
         * @octdoc  m:collection/unserialize
         * @param   string                      Data to unserialize as collection.
         */
        public function unserialize($data)
        /**/
        {
            $this->storage = unserialize($data);
            $this->keys    = array_keys($this->storage);
        }
        
        /** JsonSerializable **/
        
        /**
         * Get's called when something wants to json-serialize the collection.
         *
         * @octdoc  m:collection/jsonSerialize
         * @return  string                      Json-serialized content of collection.
         */
        public function jsonSerialize()
        /**/
        {
            return json_encode($this->storage);
        }
        
        /** Countable **/
        
        /**
         * Return number of items in collection.
         *
         * @octdoc  m:collection/count
         * @return  int                         Number of items.
         */
        public function count()
        /**/
        {
            return count($this->storage);
        }

        /** Special collection functionality **/

        /**
         * Returns value for item stored in the collection at the specified position.
         *
         * @octdoc  m:collection/getValue
         * @param   int         $position       Position to return value of item for.
         * @return  mixed                       Value stored at the specified position.
         */
        public function getValue($position)
        /**/
        {
            return $this->offsetGet($this->keys[$position]);
        }
        
        /**
         * Returns item key for specified position in the collection.
         *
         * @octdoc  m:collection/getKey
         * @param   int         $position       Position to return key of item for.
         * @return  mixed                       Key of the item at specified position.
         */
        public function getKey($position)
        /**/
        {
            return $this->keys[$position];
        }
        
        /**
         * Checks if the specified position points to an element in the collection.
         *
         * @octdoc  m:collection/isValid
         * @param   int         $position       Position to check.
         * @return  true                        Returns tue if an element exists at specified position. Returns false in case of an error.
         */
        public function isValid($position)
        /**/
        {
            return array_key_exists($position, $this->keys);
        }

        /**
         * Exchange the array for another one.
         *
         * @octdoc  m:collection/exchangeArray
         * @param   mixed       $value      The new array or object to exchange to current data with.
         * @return  array                   Data stored in collection
         */
        public function exchangeArray($value)
        /**/
        {
            if (($tmp = static::normalize($value)) === false) {
                // not an array
                throw new \Exception('don\'t know how to handle parameter of type "' . gettype($tmp) . '"');
            } else {
                $this->keys = array_keys($tmp);
            
                $return = $this->storage;
                $this->storage = $tmp;
            }
            
            return $return;
        }

        /** Static functions to work with arrays and collections **/
        
        /**
         * This method converts the input type to an array. The method can handle the following types:
         *
         *  * null -- an empty array is returned
         *  * scalar -- will be splitted by it's characters (UTF-8 safe)
         *  * array -- is returned as array
         *  * ArrayObject, ArrayIterator, \org\octris\core\type\collection, \org\octris\core\type\iterator -- get converted to an array
         *
         * for all other types 'false' is returned.
         *
         * @octdoc  m:collection/normalize
         * @param   mixed       $value          Value to normalize
         * @param   bool        $strict         If this optional parameter is set to true, scalars and null values will not
         *                                      be normalized, but will return false instead.
         * @return  array|bool                  Returns an array if normalization succeeded. In case of an error 'false' is returned.
         */
        public static function normalize($value, $strict = false)
        /**/
        {
            if (!$strict && is_null($value)) {
                // initialize empty array if no value is specified
                $return = array();
            } elseif (!$strict && is_scalar($value)) {
                // a scalar will be splitted into it's character, UTF-8 safe.
                $return = \org\octris\core\type\string::str_split((string)$value, 1);
            } elseif ($value instanceof \ArrayObject || $value instanceof \ArrayIterator || $value instanceof \org\octris\core\type\iterator || $value instanceof \org\octris\core\type\collection) {
                // an ArrayObject or ArrayIterator will be casted to a PHP array first
                $return = $value->getArrayCopy();
            } elseif (is_object($value)) {
                $return = get_object_vars($value);
            } elseif (is_array($value)) {
                $return = $value;
            } else {
                $return = false;
            }
            
            return $return;
        }

        /**
         * Return keys of array / collection.
         *
         * @octdoc  m:collection/keys
         * @param   mixed       $p                      Either an array or an object which implements the getArrayCopy method.
         * @return  array|bool                          Array of stored keys or false.
         */
        public static function keys($p)
        /**/
        {
            return (($p = static::normalize($p, true)) !== false ? array_keys($p) : false);
        }

        /**
         * Return values of array / collection.
         *
         * @octdoc  m:collection/values
         * @param   mixed       $p                      Either an array or an object which implements the getArrayCopy method.
         * @return  array|bool                          Array of stored keys or false.
         */
        public static function values($p)
        /**/
        {
            return (($p = static::normalize($p, true)) !== false ? array_values($p) : false);
        }

        /**
         * Merge multiple arrays / collections. The public static function returns either an array or an collection depending on the type of the 
         * first argument.
         *
         * @octdoc  m:collection/merge
         * @param   mixed       $arg1, ...                              Array(s) / collection(s) to merge.
         * @return  array|\org\octris\core\type\collection|bool         Merged array data or false.
         */
        public static function merge($arg1)
        /**/
        {
            $is_collection = (is_object($arg1) && $arg1 instanceof \org\octris\core\type\collection);

            if (($arg1 = static::normalize($arg1, true)) === false) {
                return false;
            }

            $args = func_get_args();
            array_shift($args);

            for ($i = 0, $cnt = count($args); $i < $cnt; ++$i) {
                if (($arg = static::normalize($args[$i], true)) !== false) {
                    $arg1 = array_merge($arg1, $arg);
                }
            }

            if ($is_collection) {
                $arg1 = new \org\octris\core\type\collection($arg1);
            }

            return $arg1;
        }

        /**
         * Rename keys of collection but preserve the ordering of the collection.
         *
         * @octdoc  m:collection/keyrename
         * @param   array                                       $data       Data to rename keys of.
         * @param   array                                       $map        Map of origin name to new name.
         * @return  array|\org\octris\core\collection|bool                  Collection/array of data with renamed keys or false in case of an error.
         */
        public static function keyrename($data, array $map)
        /**/
        {
            $is_collection = (is_object($data) && $data instanceof \org\octris\core\type\collection);

            if (($data = static::normalize($data, true)) === false) {
                return false;
            }

            $data = array_combine(array_map(function($v) use ($map) {
                return (isset($map[$v])
                        ? $map[$v]
                        : $v);
            }, array_keys($data)), array_values($data));
            
            if ($is_collection) {
                $data = new \org\octris\core\type\collection($data);
            }

            return $data;
        }

        /**
         * Applies the callback to the elements of the given arrays.
         *
         * @octdoc  m:collection/map
         * @param   callable    $cb                 Callback to apply to each element.
         * @param   mixed       $arg1, ...          The input array(s), ArrayObject(s) and / or collection(s).
         * @return  array                           Returns an array containing all the elements of arg1 after applying the
         *                                          callback public static function to each one.
         */
        public static function map(callable $cb, $arg1)
        /**/
        {
            $args = func_get_args();
            array_shift($args);
            $cnt = count($args);

            $is_collection = (is_object($arg1) && $arg1 instanceof \org\octris\core\type\collection);

            $data = array();
            $next = function() use (&$args, $cnt) {
                $return = array();
                $valid  = false;

                for ($i = 0; $i < $cnt; ++$i) {
                    if (list($k, $v) = each($args[$i])) {
                        $return[] = $v;
                        $valid = true;
                    } else {
                        $return[] = null;
                    }
                }

                return ($valid ? $return : false);
            };

            while ($tmp = $next()) {
                $data[] = call_user_func_array($cb, $tmp);
            }

            if ($is_collection) {
                $data = new \org\octris\core\type\collection($data);
            }

            return $data;
        }

        /**
         * Apply a user public static function to every member of an array. 
         *
         * @octdoc  m:collection/walk
         * @param   mixed       $arg                The input array, ArrayObject or collection.
         * @param   callable    $cb                 Callback to apply to each element.
         * @param   mixed       $userdata           Optional userdata parameter will be passed as the third parameter to the 
         *                                          callback function.
         * @return  bool                            Returns TRUE on success or FALSE on failure.
         */
        public static function walk(&$arg, callable $cb, $userdata = null)
        /**/
        {
            $data = $arg;

            $is_collection = (is_object($data) && $data instanceof \org\octris\core\type\collection);

            if (!is_scalar($key) || ($data = static::normalize($data, true)) === false) {
                return false;
            }

            array_walk($data, $cb, $userdata);

            if ($is_collection) {
                $arg = new \org\octris\core\type\collection($data);
            } else {
                $arg = $data;
            }
        }

        /**
         * Extract part of a nested array specified with key.
         *
         * @octdoc  m:collection/pluck
         * @param   mixed       $data               The input array, ArrayObject or collection.
         * @param   mixed       $key                The key -- integer or string.
         * @return  bool|mixed                      False in case of an error, otherwise and array or collection object.
         */
        public static function pluck(array $data, $key)
        /**/
        {
            $is_collection = (is_object($data) && $data instanceof \org\octris\core\type\collection);

            if (!is_scalar($key) || ($data = static::normalize($data, true)) === false) {
                return false;
            }

            $return = array();

            foreach ($data as $v) {
                if (is_array($v) && array_key_exists($key, $v)) {
                    $return[] = $v[$key];
                }
            }

            if ($is_collection) {
                $return = new \org\octris\core\type\collection($return);
            }

            return $return;
        }
    }
}

