<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\type {
    /**
     * Implements a recursive iterator for DOM Trees.
     *
     * @octdoc      c:type/domiterator
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class domiterator implements \RecursiveIterator, \SeekableIterator, \Countable
    /**/
    {
        /**
         * List of nodes to iterate.
         *
         * @octdoc  p:domiterator/$nodes
         * @type    \DOMNodeList
         */
        protected $nodes;
        /**/

        /**
         * Iterator position.
         *
         * @octdoc  p:domiterator/$position
         * @type    int
         */
        protected $position = 0;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:domiterator/__construct
         * @param   DOMNodeList                     $nodes                  Nodes to iterate.
         */
        public function __construct(DOMNodeList $nodes)
        /**/
        {
            $this->nodes = $nodes;
        }

        /**
         * Return item from collection the iterator is pointing to.
         *
         * @octdoc  m:domiterator/current
         * @return  DOMNode                                                 Current item.
         */
        public function current()
        /**/
        {
            return $this->nodes->item($this->position);
        }

        /**
         * Return iterator position.
         *
         * @octdoc  m:domiterator/key
         * @return  int                                                     Iterator position.
         */
        public function key()
        /**/
        {
            return $this->position;
        }

        /**
         * Rewind iterator to beginning.
         *
         * @octdoc  m:domiterator/rewind
         */
        public function rewind()
        /**/
        {
            $this->position = 0;
        }

        /**
         * Advance the iterator by 1.
         *
         * @octdoc  m:domiterator/next
         */
        public function next()
        /**/
        {
            ++$this->position;
        }

        /**
         * Checks if the position resolves to a node in the node list.
         *
         * @octdoc  m:domiterator/valid
         * @return  bool                                                    Returns true, if position is valid.
         */
        public function valid()
        /**/
        {
            return ($this->position < $this->nodes->length);
        }

        /**
         * Move iterator position to specified position.
         *
         * @octdoc  m:domiterator/seek
         * @param   int                             $position               Position to move iterator to.
         */
        public function seek($position)
        /**/
        {
            $this->position = $position;
        }

        /**
         * Count the elements in the node list.
         *
         * @octdoc  m:domiterator/count
         * @return  int                                                     Number of nodes stored in the node list.
         */
        public function count()
        /**/
        {
            return $this->nodes->length;
        }

        /**
         * Returns a new iterator instance for the current node.
         *
         * @octdoc  m:domiterator/getChildren
         * @return  \org\octris\core\type\domiterator                       Instance domiterator.
         */
        public function getChildren()
        /**/
        {
            return new static($this->current()->nodeList);
        }

        /**
         * Checks whether the current node has children.
         *
         * @octdoc  m:domiterator/hasChildren
         * @param   bool                                                    Returns true, if the current node has children.
         */
        public function hasChildren()
        /**/
        {
            return $this->current()->hasChildNodes();
        }
    }
}
