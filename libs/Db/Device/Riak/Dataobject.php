<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Device\Riak;

/**
 * Riak data object
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class DataObject extends \Octris\Core\Db\Type\DataObject
{
    /**
     * Headers stored with object.
     *
     * @type    array
     */
    protected $headers;

    /**
     * Content type of stored data.
     *
     * @type    string
     */
    protected $content_type = 'application/json';

    /**
     * Constructor.
     *
     * @param   \Octris\Core\Db\Device\Riak         $device         Device the connection belongs to.
     * @param   string                                  $collection     Name of collection the dataobject belongs to.
     * @param   array                                   $data           Data to initialize dataobject with,
     */
    public function __construct(\Octris\Core\Db\Device\Riak $device, $collection, array $data = array())
    {
        parent::__construct($device, $collection, $data);
    }

    /**
     * Set type of content of data stored in the object.
     *
     * @param   string                  $content_type               Content type to set.
     */
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }

    /**
     * Return content type of data stored in the object.
     *
     * @return  string                                              Content type to return.
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * Get datetime of last modification of the object. The method returns 'null' if the
     * last modified datetime is not set.
     *
     * @return  \DateTime|null                                      Last modified datetime.
     */
    public function getLastModified()
    {
        return (isset($this->headers['last-modified'])
                ? new DateTime($this->headers['last-modified'])
                : null);
    }

    /** Type casting **/

    /**
     * Cast a PHP type to DB internal type.
     *
     * @param   mixed               $value              Value to cast.
     * @param   string              $name               Name of the value in the data structure.
     * @return  mixed                                   Casted value.
     */
    protected function castPhpToDb($value, $name)
    {
        if (is_object($value)) {
            if ($value instanceof \Octris\Core\Type\Number) {
                // number -> float -or- int
                $return = ($value->isDecimal()
                            ? (float)(string)$value
                            : (int)(string)$value);
            } elseif ($value instanceof \Octris\Core\Type\Money) {
                // money -> float
                $return = (float)(string)$value;
            } elseif ($value instanceof \DateTime) {
                // datetime -> string
                $return = $value->format('Y-m-d H:M:S');
            } elseif ($value instanceof \Octris\Core\Db\Type\Dbref) {
                $return = $value;
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
     * @param   mixed               $value              Value to cast.
     * @param   string              $name               Name of the value in the data structure.
     * @return  mixed                                   Casted value.
     */
    protected function castDbToPhp($value, $name)
    {
        return $value;
    }

    /**
     * Recursive data iteration and casting for preparing data for export to database. This method
     * overwrites the default export function to filter all database references from the data.
     *
     * @param   array               $data               Data to process.
     */
    protected function export(array &$data)
    {
        // filter
        $filter = function ($data) use (&$filter) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = $filter($value);
                } elseif (is_object($value) && $value instanceof \Octris\Core\Db\Type\Dbref) {
                    unset($data[$key]);
                }
            }

            return $data;
        };

        $data = $filter($data);

        // cast
        parent::export($data);
    }
}
