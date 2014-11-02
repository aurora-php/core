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
     * @type    \octris\core\db\device\mongodb
     */
    protected $device;

    /**
     * Name of collection the result belongs to.
     *
     * @type    string
     */
    protected $collection;

    /**
     * MongoDB result cursor.
     *
     * @type    \MongoCursor
     */
    protected $cursor;

    /**
     * Constructor.
     *
     * @param   \Octris\Core\Db\Device\Mongodb  $device         Device the connection belongs to.
     * @param   string                              $collection     Name of collection the result belongs to.
     * @param   \MongoCursor                        $cursor         Cursor of query result.
     */
    public function __construct(\Octris\Core\Db\Device $device, $collection, \MongoCursor $cursor)
    {
        $this->device     = $device;
        $this->collection = $collection;
        $this->cursor     = $cursor;

        if ($this->cursor->hasNext()) {
            $this->cursor->next();
        }
    }

    /**
     * Count number of items in the result set.
     *
     * @return  int                                         Number of items in the result-set.
     */
    public function count()
    {
        return $this->cursor->count();
    }

    /**
     * Return current item of the search result.
     *
     * @return  \octris\core\db\device\mongodb\dataobject|bool  Returns either a dataobject with the stored contents of the current item or false, if the cursor position is invalid.
     */
    public function current()
    {
        if (!$this->valid()) {
            $return = null;
        } else {
            $return = new \Octris\Core\Db\Device\Mongodb\Dataobject(
                $this->device,
                $this->collection,
                $this->cursor->current()
            );
        }

        return $return;
    }

    /**
     * Advance cursor to the next item.
     */
    public function next()
    {
        $this->cursor->next();
    }

    /**
     * Returns the object-ID of the current search result item.
     *
     * @return  string                                      Object-ID.
     */
    public function key()
    {
        return $this->cursor->key();
    }

    /**
     * Rewind cursor.
     */
    public function rewind()
    {
        $this->cursor->rewind();
    }

    /**
     * Tests if cursor position is valid.
     *
     * @return  bool                                        Returns true, if cursor position is valid.
     */
    public function valid()
    {
        return $this->cursor->valid();
    }
}
