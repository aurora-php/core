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
 * MongoDB data object
 *
 * @octdoc      c:mongodb/dataobject
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Dataobject extends \Octris\Core\Db\Type\Dataobject
{
    /**
     * Constructor.
     *
     * @octdoc  m:dataobject/__construct
     * @param   \Octris\Core\Db\Device\Mongodb      $device         Device the connection belongs to.
     * @param   string                                  $collection     Name of collection the dataobject belongs to.
     * @param   array                                   $data           Data to initialize dataobject with,
     */
    public function __construct(\Octris\Core\Db\Device\Mongodb $device, $collection, array $data = array())
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
    {
        if ($name == '_id') {
            // _id -> MongoId
            $return = new \MongoId($value);
        } elseif (is_object($value)) {
            if ($value instanceof \octris\core\type\number) {
                // number -> float -or- MongoInt64
                $return = ($value->isDecimal()
                            ? (float)(string)$value
                            : new \MongoInt64((string)$value));
            } elseif ($value instanceof \octris\core\type\money) {
                // money -> float
               $return = (float)(string)$value;
            } elseif ($value instanceof \DateTime) {
                // datetime -> MongoDate
                $tmp = explode('.', $value->format('U.u'));

                $return = new \MongoDate($tmp[0], $tmp[1]);
            } elseif ($value instanceof \octris\core\db\type\dbref) {
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
    {
        if (is_object($value)) {
            if ($value instanceof \MongoDate) {
                $return = new \Octris\Core\Type\Datetime((float)($value->sec . '.' . $value->usec));
            } elseif ($value instanceof \MongoId) {
                $return = (string)$value;
            } elseif ($value instanceof \MongoInt32) {
                $return = new \Octris\Core\Type\Number((string)$value);
            } elseif ($value instanceof \MongoInt64) {
                $return = new \Octris\Core\Type\Number((string)$value);
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
    {
        $process = function (&$data) use (&$process) {
            array_walk($data, function (&$value, $name) {
                if (is_array($value)) {
                    if (\MongoDBRef::isRef($value)) {
                        $value = new \Octris\Core\Db\Type\Dbref(
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
