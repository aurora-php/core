<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\db\device\sqlite;

/**
 * Query result object.
 *
 * @octdoc      c:sqlite/result
 * @copyright   copyright (c) 2013 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class result implements \Iterator
{
    /**
     * Device the result belongs to.
     *
     * @octdoc  p:result/$device
     * @type    \octris\core\db\device\sqlite
     */
    protected $device;
    /**/

    /**
     * Name of collection the result belongs to. Contains 'null', if the
     * result cannot be assigned to a single collection.
     *
     * @octdoc  p:result/$collection
     * @type    string|null
     */
    protected $collection = null;
    /**/

    /**
     * SQLite result instance.
     *
     * @octdoc  p:result/$result
     * @type    \SQLite3
     */
    protected $result;
    /**/

    /**
     * Row data of current position.
     *
     * @octdoc  p:result/$position
     * @type    array
     */
    protected $row = array();
    /**/

    /**
     * Current position in result.
     *
     * @octdoc  p:result/$position
     * @type    int
     */
    protected $position = 0;
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:result/__construct
     * @param   \octris\core\db\device\sqlite   $device         Device the connection belongs to.
     * @param   \SQLite3Result                      $result         Instance of sqlite result class.
     * @param   string                              $collection     Name of collection the result belongs to.
     */
    public function __construct(\octris\core\db\device\sqlite $device, \SQLite3Result $result, $collection = null)
    {
        $this->device     = $device;
        $this->collection = $collection;
        $this->result     = $result;
    }

    /**
     * Return current item of the search result.
     *
     * @octdoc  m:result/current
     * @return  \octris\core\db\device\riak\dataobject|array|bool  Returns either a dataobject or array with the stored contents of the current item or false, if the cursor position is invalid.
     */
    public function current()
    {
        if (!$this->valid()) {
            $return = null;
        } elseif (is_null($this->collection)) {
            $return = $this->row;
        } else {
            $return = new \octris\core\db\device\sqlite\dataobject(
                $this->device, 
                $this->collection,
                $this->row
            );
        }

        return $return;
    }

    /**
     * Advance cursor to the next item.
     *
     * @octdoc  m:result/next
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Returns the object-ID of the current search result item.
     *
     * @octdoc  m:result/key
     * @return  string|null                                     Object-ID.
     */
    public function key()
    {
        return null;
    }

    /**
     * Rewind cursor.
     *
     * @octdoc  m:result/rewind
     */
    public function rewind()
    {
        $this->position = 0;
        $this->result->reset();
    }

    /**
     * Tests if cursor position is valid.
     *
     * @octdoc  m:result/valid
     * @return  bool                                        Returns true, if cursor position is valid.
     */
    public function valid()
    {
        if (($result = $this->result->fetchArray(SQLITE3_ASSOC))) {
            $this->row = $result;
        } else {
            $this->row = array();
        }
        
        return !!$result;
    }
}

