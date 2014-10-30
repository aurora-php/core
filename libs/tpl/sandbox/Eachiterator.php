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
 * @octdoc      c:sandbox/eachiterator
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Eachiterator implements \Iterator
{
    /**
     * Iterator object.
     *
     * @octdoc  p:eachiterator/$iterator
     * @type    \Traversable
     */
    protected $iterator;
    /**/

    /**
     * Iterator position.
     *
     * @octdoc  p:eachiterator/$position
     * @type    int
     */
    protected $position = 0;
    /**/

    /**
     * Number of items in iterator object.
     *
     * @octdoc  p:eachiterator/$count
     * @type    int|null
     */
    protected $items = null;
    /**/

    /**
     * Whether the object to iterate is a generator.
     *
     * @octdoc  p:eachiterator/$is_generator
     * @type    bool
     */
    protected $is_generator = false;
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:eachiterator/__construct
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
     * @octdoc  m:eachiterator/getMeta
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
     * @octdoc  m:eachiterator/current
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
     * @octdoc  m:eachiterator/key
     * @return  mixed                                                   Key.
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * Rewind iterator to beginning.
     *
     * @octdoc  m:eachiterator/rewind
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
     *
     * @octdoc  m:eachiterator/next
     */
    public function next()
    {
        $this->iterator->next();
        ++$this->position;
    }

    /**
     * Checks if the position in the collection the iterator points to is valid.
     *
     * @octdoc  m:eachiterator/valid
     * @return  bool                                                    Returns true, if position is valid.
     */
    public function valid()
    {
        return $this->iterator->valid();
    }
}
