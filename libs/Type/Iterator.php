<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Type;

/**
 * Implements an ArrayIterator for \octris\core\type\collection.
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Iterator implements \Iterator, \SeekableIterator, \Countable
{
    /**
     * Instance of collection the iterator accesses.
     *
     * @type    \octris\core\type\collection
     */
    protected $collection;
    
    /**
     * Iterator position.
     *
     * @type    int
     */
    protected $position = 0;
    
    /**
     * Constructor.
     *
     * @param   \Octris\Core\Type\Collection    $collection         Instance of collection to access.
     */
    public function __construct(\Octris\Core\Type\Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Return item from collection the iterator is pointing to.
     *
     * @return  mixed                                                   Item.
     */
    public function current()
    {
        return $this->collection->getValue($this->position);
    }

    /**
     * Return key of item of collection the iterator is pointing to.
     *
     * @return  mixed                                                   Key.
     */
    public function key()
    {
        return $this->collection->getKey($this->position);
    }

    /**
     * Rewind iterator to beginning.
     *
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Advance the iterator by 1.
     *
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Checks if the position in the collection the iterator points to is valid.
     *
     * @return  bool                                                    Returns true, if position is valid.
     */
    public function valid()
    {
        return $this->collection->isValid($this->position);
    }

    /**
     * Move iterator position to specified position.
     *
     * @param   int         $position                                   Position to move iterator to.
     */
    public function seek($position)
    {
        $this->position = $position;
    }

    /**
     * Count the elements in the collection.
     *
     * @return  int                                                     Number of items stored in the collection.
     */
    public function count()
    {
        return count($this->collection);
    }

    /** Special iterator methods **/

    /**
     * Returns the current position of the iterator.
     *
     * @return  int                                                     Current iterator position.
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Returns a copy of the data stored in collection.
     *
     * @return  array                                                   Data stored in collection.
     */
    public function getArrayCopy()
    {
        return $this->collection->getArrayCopy();
    }
}
