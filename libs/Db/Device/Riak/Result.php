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
 * Query result object.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Result implements \Iterator, \Countable
{
    /**
     * Device the result belongs to.
     *
     * @type    \Octris\Core\Db\Device\Riak
     */
    protected $device;

    /**
     * Name of collection the result belongs to.
     *
     * @type    string
     */
    protected $collection;

    /**
     * Array of result.
     *
     * @type    array
     */
    protected $result = array();

    /**
     * Current position in array.
     *
     * @type    int
     */
    protected $position = 0;

    /**
     * Constructor.
     *
     * @param   \Octris\Core\Db\Device\Riak     $device         Device the connection belongs to.
     * @param   string                              $collection     Name of collection the result belongs to.
     * @param   array                               $result         Query result.
     */
    public function __construct(\Octris\Core\Db\Device\Riak $device, $collection, $result)
    {
        $this->device     = $device;
        $this->collection = $collection;
        $this->result     = $result['response']['docs'];
    }

    /**
     * Count number of items in the result set.
     *
     * @return  int                                         Number of items in the result-set.
     */
    public function count()
    {
        return count($this->result);
    }

    /**
     * Return current item of the search result.
     *
     * @return  \Octris\Core\Db\Device\Riak\Dataobject|bool  Returns either a dataobject with the stored contents of the current item or false, if the cursor position is invalid.
     */
    public function current()
    {
        if (!$this->valid()) {
            $return = null;
        } else {
            $data = $this->result[$this->position]['fields'];
            $data['_id'] = $this->result[$this->position]['id'];

            $return = new \Octris\Core\Db\Device\Riak\Dataobject(
                $this->device,
                $this->collection,
                $data
            );
        }

        return $return;
    }

    /**
     * Advance cursor to the next item.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Returns the object-ID of the current search result item.
     *
     * @return  string                                      Object-ID.
     */
    public function key()
    {
        $this->result[$this->position]['id'];
    }

    /**
     * Rewind cursor.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Tests if cursor position is valid.
     *
     * @return  bool                                        Returns true, if cursor position is valid.
     */
    public function valid()
    {
        return array_key_exists($this->position, $this->result);
    }
}
