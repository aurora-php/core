<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * Type superclass.
     *
     * @octdoc      c:core/type
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class type
    /**/
    {
        /**
         * Cast input value to a specified type. In contrast to PHP's built-in settype function this one will try to cast arrays
         * and objects to the template collection type. Additionally this function supports casting explicitly to the template
         * collection type. All other types are handed over to PHP's built-in settype function.
         *
         * @octdoc  m:type/settype
         * @param   mixed       $val            Value to cast.
         * @param   string      $type           Type to cast to.
         * @return  mixed                       Casted value.
         */
        public static function settype($val, $type)
        /**/
        {
            $type = strtolower($type);

            if ($type == 'array' || $type == 'object') {
                if (is_object($val)) {
                    if ($val instanceof \org\octris\core\type\collection) {
                        $val = $val->getArrayCopy();
                    } else {
                        $val = (array)$val;
                    }
                } elseif (!is_array($val)) {
                    $val = array($val);
                }

                if ($type == 'object') {
                    $val = (object)$val;
                }
            } elseif ($type == 'collection') {
                $val = new \org\octris\core\type\collection($val);
            } elseif ($type == 'money') {
                if (!is_object($val) || !($val instanceof \org\octris\core\type\money)) {
                    // parameter is not a money object
                    if (!is_numeric($money)) {
                        // parameter is not a valid numeric value
                        $val = 0;
                    }
                
                    $val = new \org\octris\core\type\money($val);
                }
            } else {
                \settype($val, $type);
            }

            return $val;
        }
    }
}
