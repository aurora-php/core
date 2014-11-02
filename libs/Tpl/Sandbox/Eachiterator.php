<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Tpl\Sandbox;

/**
 * Implements an iterator for iterating in template sandbox using 'each'.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Eachiterator implements \Iterator
{
    /**
     * Iterator object.
     *
     * @type    \Traversable
     */
    protected $iterator;

    /**
     * Iterator position.
     *
     * @type    int
     */
    protected $position = 0;

    /**
     * Number of items in iterator object.
     *
     * @type    int|null
     */
    protected $items = null;

    /**
     * Whether the object to iterate is a generator.
     *
     * @type    bool
     */
    protected $is_generator = false;

    /**
     * Constructor.
     *
     * @param   array|\Traversable            $object                   Array or traversable object to iterate.
     */
    public function __construct($object)
    {
        if (!($object instanceof \Traversable)) {
            $object = new \Octris\Core\Type\Collection($object);
        }

        $this->iterator = ($object instanceof \IteratorAggregate
                            ? $object->getIterator()
                            : $object);

        $this->is_generator = ($object instanceof \Generator);

        if ($object instanceof \Countable) {
            $this->items = count($object);
        }
    }

    /**
     * Get meta information about current position.
     *
     * @return  array                                                   Array with meta information.
     */
    public function getMeta()
    {
        return array(
            'key'       => $this->key(),
            'pos'       => $this->position,
            'count'     => $this->items,
            'is_first'  => ($this->position == 0),
            'is_last'   => (!is_null($this->items) && $this->position == $this->items - 1)
        );
    }

    /** Iterator **/

    /**
     * Return current item.
     *
     * @return  mixed                                                   Item.
     */
    public function current()
    {
        $tmp = $this->iterator->current();

        if (!is_scalar($tmp) && !(is_object($tmp) && $tmp instanceof \Traversable)) {
            $tmp = new \Octris\Core\Type\Collection($tmp);
        }

        return $tmp;
    }

    /**
     * Return current key.
     *
     * @return  mixed                                                   Key.
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * Rewind iterator to beginning.
     *
     * @todo    write a notice to some log-file, if a generator throws an exception
     */
    public function rewind()
    {
        try {
            $this->iterator->rewind();
            $this->position = 0;
        } catch (\Exception $e) {
            if (!$this->is_generator) {
                throw $e;
            }
        }
    }

    /**
     * Advance the iterator by 1.
     */
    public function next()
    {
        $this->iterator->next();
        ++$this->position;
    }

    /**
     * Checks if the position in the collection the iterator points to is valid.
     *
     * @return  bool                                                    Returns true, if position is valid.
     */
    public function valid()
    {
        return $this->iterator->valid();
    }
}
