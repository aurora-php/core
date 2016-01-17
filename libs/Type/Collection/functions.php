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
 * Functions to work with arrays and collections.
 *
 * @copyright   Copyright (c) 2010-2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */

/**
 * This method converts the input type to an array. The method can handle the following types:
 *
 *  * null -- an empty array is returned
 *  * scalar -- will be splitted by it's characters (UTF-8 safe)
 *  * array -- is returned as array
 *  * object -- object variables are extracted returned as array
 *  * ArrayObject, ArrayIterator, \Octris\Core\Type\Collection -- get converted to an array
 *
 * for all other types 'false' is returned.
 *
 * @param   mixed       $value          Value to normalize
 * @param   bool        $strict         If this optional parameter is set to true, scalars and null values will not
 *                                      be normalized, but will return false instead.
 * @return  array|bool                  Returns an array if normalization succeeded. In case of an error 'false' is returned.
 */
function Normalize($value, $strict = false)
{
    if (!$strict && is_null($value)) {
        // initialize empty array if no value is specified
        $return = array();
    } elseif (!$strict && is_scalar($value)) {
        // a scalar will be splitted into it's character, UTF-8 safe.
        $return = \Octris\Core\Type\String::str_split((string)$value, 1);
    } elseif ($value instanceof \ArrayObject || $value instanceof \ArrayIterator || $value instanceof \Octris\Core\Type\Iterator || $value instanceof \Octris\Core\Type\Collection) {
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
 * @param   mixed       $p                      Either an array or an object which implements the getArrayCopy method.
 * @return  array|bool                          Array of stored keys or false.
 */
function Keys($p)
{
    return (($p = Normalize($p, true)) !== false ? array_keys($p) : false);
}

/**
 * Return values of array / collection.
 *
 * @param   mixed       $p                      Either an array or an object which implements the getArrayCopy method.
 * @return  array|bool                          Array of stored keys or false.
 */
function Values($p)
{
    return (($p = Normalize($p, true)) !== false ? array_values($p) : false);
}

/**
 * Merge multiple arrays / collections. The function returns either an array or an collection depending on the type of the
 * first argument.
 *
 * @param   mixed       $arg1, ...                              Array(s) / collection(s) to merge.
 * @return  array|\Octris\Core\Type\Collection\collection|bool         Merged array data or false.
 */
function Merge($arg1)
{
    $is_collection = (is_object($arg1) && $arg1 instanceof \Octris\Core\Type\Collection);

    if (($arg1 = Normalize($arg1, true)) === false) {
        return false;
    }

    $args = func_get_args();
    array_shift($args);

    for ($i = 0, $cnt = count($args); $i < $cnt; ++$i) {
        if (($arg = Normalize($args[$i], true)) !== false) {
            $arg1 = array_merge($arg1, $arg);
        }
    }

    if ($is_collection) {
        $arg1 = new \Octris\Core\Type\Collection($arg1);
    }

    return $arg1;
}

/**
 * Rename keys of collection but preserve the ordering of the collection.
 *
 * @param   array                                       $data       Data to rename keys of.
 * @param   array                                       $map        Map of origin name to new name.
 * @return  array|\Octris\Core\Collection|bool                      Collection/array of data with renamed keys or false in case of an error.
 */
function KeyRename($data, array $map)
{
    $is_collection = (is_object($data) && $data instanceof \Octris\Core\Type\Collection);

    if (($data = Normalize($data, true)) === false) {
        return false;
    }

    $data = array_combine(array_map(function ($v) use ($map) {
        return (isset($map[$v])
                ? $map[$v]
                : $v);
    }, array_keys($data)), array_values($data));

    if ($is_collection) {
        $data = new \Octris\Core\Type\Collection($data);
    }

    return $data;
}

/**
 * Applies the callback to the elements of the given arrays.
 *
 * @param   callable    $cb                 Callback to apply to each element.
 * @param   mixed       $arg1, ...          The input array(s), ArrayObject(s) and / or collection(s).
 * @return  array                           Returns an array containing all the elements of arg1 after applying the
 *                                          callback function to each one.
 */
function Map(callable $cb, $arg1)
{
    $args = func_get_args();
    array_shift($args);
    $cnt = count($args);

    $is_collection = (is_object($arg1) && $arg1 instanceof \Octris\Core\Type\Collection);

    $data = array();
    $next = function () use (&$args, $cnt) {
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
        $data = new \Octris\Core\Type\Collection($data);
    }

    return $data;
}

/**
 * Apply a user function to every member of an array.
 *
 * @param   mixed       $arg                The input array, ArrayObject or collection.
 * @param   callable    $cb                 Callback to apply to each element.
 * @param   mixed       $userdata           Optional userdata parameter will be passed as the third parameter to the
 *                                          callback function.
 * @return  bool                            Returns TRUE on success or FALSE on failure.
 */
function Walk(&$arg, callable $cb, $userdata = null)
{
    $data = $arg;

    $is_collection = (is_object($data) && $data instanceof \Octris\Core\Type\Collection);

    if (!is_scalar($key) || ($data = Normalize($data, true)) === false) {
        return false;
    }

    array_walk($data, $cb, $userdata);

    if ($is_collection) {
        $arg = new \Octris\Core\Type\Collection($data);
    } else {
        $arg = $data;
    }
}

/**
 * Extract part of a nested array specified with key.
 *
 * @param   mixed       $data               The input array, ArrayObject or collection.
 * @param   mixed       $key                The key -- integer or string.
 * @return  bool|mixed                      False in case of an error, otherwise and array or collection object.
 */
function Pluck(array $data, $key)
{
    $is_collection = (is_object($data) && $data instanceof \Octris\Core\Type\Collection);

    if (!is_scalar($key) || ($data = Normalize($data, true)) === false) {
        return false;
    }

    $return = array();

    foreach ($data as $v) {
        if (is_array($v) && array_key_exists($key, $v)) {
            $return[] = $v[$key];
        }
    }

    if ($is_collection) {
        $return = new \Octris\Core\Type\Collection($return);
    }

    return $return;
}
