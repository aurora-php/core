<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Type;

/**
 * Iterator for recursive iterating data objects of query results
 *
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Dataiterator implements \Iterator
{
    /**
     * The dataobject to iterate.
     *
     * @type    \octris\core\db\type\subobject
     */
    protected $data;
    
    /**
     * Keys stored in dataobject.
     *
     * @type    array
     */
    protected $keys;
    
    /**
     * Internal pointer position.
     *
     * @type    int
     */
    protected $position = 0;
    
    /**
     * Constructor.
     *
     * @parem   \Octris\Core\Db\Type\Subobject    $dataobject         The dataobject to iterate.
     */
    public function __construct(\Octris\Core\Db\Type\Subobject $dataobject)
    {
        $this->data = $dataobject;
        $this->keys = $dataobject->getKeys();
    }

    /** Iterator **/

    /**
     * Get value of item.
     *
     * @return  mixed                                                               Value stored at current position.
     */
    public function current()
    {
        return $this->data[$this->keys[$this->position]];
    }

    /**
     * Get key of current item.
     *
     * @return  scalar                                                              Key of current position.
     */
    public function key()
    {
        return $this->keys[$this->position];
    }

    /**
     * Advance pointer.
     *
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Reset pointer.
     *
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Test if current pointer position is valid.
     *
     * @return  bool                                                                True, if position is valid.
     */
    public function valid()
    {
        return isset($this->keys[$this->position]);
    }
}
