<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Device\Sqlite;

/**
 * Query result object.
 *
 * @copyright   copyright (c) 2013-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Result implements \Iterator
{
    /**
     * Device the result belongs to.
     *
     * @type    \octris\core\db\device\sqlite
     */
    protected $device;
    
    /**
     * Name of collection the result belongs to. Contains 'null', if the
     * result cannot be assigned to a single collection.
     *
     * @type    string|null
     */
    protected $collection = null;
    
    /**
     * SQLite result instance.
     *
     * @type    \SQLite3
     */
    protected $result;
    
    /**
     * Row data of current position.
     *
     * @type    array
     */
    protected $row = array();
    
    /**
     * Current position in result.
     *
     * @type    int
     */
    protected $position = 0;
    
    /**
     * Constructor.
     *
     * @param   \Octris\Core\Db\Device\Sqlite   $device         Device the connection belongs to.
     * @param   \SQLite3Result                      $result         Instance of sqlite result class.
     * @param   string                              $collection     Name of collection the result belongs to.
     */
    public function __construct(\Octris\Core\Db\Device\Sqlite $device, \SQLite3Result $result, $collection = null)
    {
        $this->device     = $device;
        $this->collection = $collection;
        $this->result     = $result;
    }

    /**
     * Return current item of the search result.
     *
     * @return  \octris\core\db\device\riak\dataobject|array|bool  Returns either a dataobject or array with the stored contents of the current item or false, if the cursor position is invalid.
     */
    public function current()
    {
        if (!$this->valid()) {
            $return = null;
        } elseif (is_null($this->collection)) {
            $return = $this->row;
        } else {
            $return = new \Octris\Core\Db\Device\Sqlite\Dataobject(
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
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Returns the object-ID of the current search result item.
     *
     * @return  string|null                                     Object-ID.
     */
    public function key()
    {
        return null;
    }

    /**
     * Rewind cursor.
     *
     */
    public function rewind()
    {
        $this->position = 0;
        $this->result->reset();
    }

    /**
     * Tests if cursor position is valid.
     *
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
