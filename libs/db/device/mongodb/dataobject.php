<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\mongodb {
    /**
     * MongoDB data object
     *
     * @octdoc      c:mongodb/dataobject
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class dataobject extends \org\octris\core\db\type\dataobject
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:dataobject/__construct
         * @param   \org\octris\core\db\device\mongodb      $device         Device the connection belongs to.
         * @param   string                                  $collection     Name of collection the dataobject belongs to.
         * @param   array                                   $data           Data to initialize dataobject with,
         */
        public function __construct(\org\octris\core\db\device\mongodb $device, $collection, array $data = array())
        /**/
        {
            parent::__construct($device, $collection, $data);
        }

        /** Type casting **/
        
        /**
         * Cast a PHP type to DB internal type.
         *
         * @octdoc  m:dataobject/castPhpToDb
         * @param   mixed               $value              Value to cast.
         * @param   string              $name               Name of the value in the data structure.
         * @return  mixed                                   Casted value.
         */
        protected function castPhpToDb($value, $name)
        /**/
        {
            if ($name == '_id') {
                // _id -> MongoId
                $return = new \MongoId($value);
            } elseif (is_object($value)) {
                if ($value instanceof \org\octris\core\type\number) {
                    // number -> float -or- MongoInt64
                    $return = ($value->isDecimal()
                                ? (float)(string)$value
                                : new \MongoInt64((string)$value));
                } elseif ($value instanceof \org\octris\core\type\money) {
                    // money -> float
                   $return = (float)(string)$value;
                } elseif ($value instanceof \DateTime) {
                    // datetime -> MongoDate
                    $tmp = explode('.', $value->format('U.u'));

                    $return = new \MongoDate($tmp[0], $tmp[1]);
                } elseif ($value instanceof \org\octris\core\db\type\dbref) {
                    // dbref -> \MongoDBRef
                    $return = \MongoDBRef::create($value->collection, $value->key);
                } else {
                    $return = (string)$value;
                }
            } else {
                $return = $value;
            }
            
            return $return;
        }
        
        /**
         * Cast a DB internal type to PHP type.
         *
         * @octdoc  m:dataobject/castDbToPhp
         * @param   mixed               $value              Value to cast.
         * @param   string              $name               Name of the value in the data structure.
         * @return  mixed                                   Casted value.
         */
        protected function castDbToPhp($value, $name)
        /**/
        {
            if (is_object($value)) {
                if ($value instanceof \MongoDate) {
                    $return = new \org\octris\core\type\datetime((float)($value->sec . '.' . $value->usec));
                } elseif ($value instanceof \MongoId) {
                    $return = (string)$value;
                } elseif ($value instanceof \MongoInt32) {
                    $return = new \org\octris\core\type\number((string)$value);
                } elseif ($value instanceof \MongoInt64) {
                    $return = new \org\octris\core\type\number((string)$value);
                } else {
                    $return = $value;
                }
            } else {
                $return = $value;
            }

            return $return;
        }
        
        /**
         * Recursive data iteration and casting for preparing data for import into dataobject.
         *
         * @octdoc  m:dataobject/import
         * @param   array               $data               Data to process.
         */
        protected function import(array &$data)
        /**/
        {
            $process = function(&$data) use (&$process) {
                array_walk($data, function(&$value, $name) {
                    if (is_array($value)) {
                        if (\MongoDBRef::isRef($value)) {
                            $value = new \org\octris\core\db\type\dbref(
                                $value['$ref'], $value['$id']
                            );
                        } else {
                            $process($a);
                        }
                    } else {
                        $value = $this->castDbToPhp($value, $name);
                    }
                });
            };
        }
    }
}
