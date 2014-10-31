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
 * Implements a recursive iterator for DOM Trees.
 *
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Domiterator implements \RecursiveIterator, \SeekableIterator, \Countable
{
    /**
     * List of nodes to iterate.
     *
     * @type    \DOMNodeList
     */
    protected $nodes;
    
    /**
     * Iterator position.
     *
     * @type    int
     */
    protected $position = 0;
    
    /**
     * Constructor.
     *
     * @param   DOMNodeList                     $nodes                  Nodes to iterate.
     */
    public function __construct(DOMNodeList $nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * Return item from collection the iterator is pointing to.
     *
     * @return  DOMNode                                                 Current item.
     */
    public function current()
    {
        return $this->nodes->item($this->position);
    }

    /**
     * Return iterator position.
     *
     * @return  int                                                     Iterator position.
     */
    public function key()
    {
        return $this->position;
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
     * Checks if the position resolves to a node in the node list.
     *
     * @return  bool                                                    Returns true, if position is valid.
     */
    public function valid()
    {
        return ($this->position < $this->nodes->length);
    }

    /**
     * Move iterator position to specified position.
     *
     * @param   int                             $position               Position to move iterator to.
     */
    public function seek($position)
    {
        $this->position = $position;
    }

    /**
     * Count the elements in the node list.
     *
     * @return  int                                                     Number of nodes stored in the node list.
     */
    public function count()
    {
        return $this->nodes->length;
    }

    /**
     * Returns a new iterator instance for the current node.
     *
     * @return  \octris\core\type\domiterator                       Instance domiterator.
     */
    public function getChildren()
    {
        return new static($this->current()->nodeList);
    }

    /**
     * Checks whether the current node has children.
     *
     * @param   bool                                                    Returns true, if the current node has children.
     */
    public function hasChildren()
    {
        return $this->current()->hasChildNodes();
    }
}
