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
     * Implements an ArrayIterator for \org\octris\core\type\collection.
     *
     * @octdoc      c:type/iterator
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class iterator implements \Iterator, \SeekableIterator, \Countable
    /**/
    {
        /**
         * Instance of collection the iterator accesses.
         *
         * @octdoc  p:iterator/$collection
         * @type    \org\octris\core\type\collection
         */
        protected $collection;
        /**/

        /**
         * Iterator position.
         *
         * @octdoc  p:iterator/$position
         * @type    int
         */
        protected $position = 0;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:iterator/__construct
         * @param   \org\octris\core\type\collection    $collection         Instance of collection to access.
         */
        public function __construct(\org\octris\core\type\collection $collection)
        /**/
        {
            $this->collection = $collection;
        }

        /**
         * Return item from collection the iterator is pointing to.
         *
         * @octdoc  m:iterator/current
         * @return  mixed                                                   Item.
         */
        public function current()
        /**/
        {
            return $this->collection->getValue($this->position);
        }

        /**
         * Return key of item of collection the iterator is pointing to.
         *
         * @octdoc  m:iterator/key
         * @return  mixed                                                   Key.
         */
        public function key()
        /**/
        {
            return $this->collection->getKey($this->position);
        }

        /**
         * Rewind iterator to beginning.
         *
         * @octdoc  m:iterator/rewind
         */
        public function rewind()
        /**/
        {
            $this->position = 0;
        }

        /**
         * Advance the iterator by 1.
         *
         * @octdoc  m:iterator/next
         */
        public function next()
        /**/
        {
            ++$this->position;
        }

        /**
         * Checks if the position in the collection the iterator points to is valid.
         *
         * @octdoc  m:iterator/valid
         * @return  bool                                                    Returns true, if position is valid.
         */
        public function valid()
        /**/
        {
            return $this->collection->isValid($this->position);
        }

        /**
         * Move iterator position to specified position.
         *
         * @octdoc  m:iterator/seek
         * @param   int         $position                                   Position to move iterator to.
         */
        public function seek($position)
        /**/
        {
            $this->position = $position;
        }

        /**
         * Count the elements in the collection.
         *
         * @octdoc  m:iterator/count
         * @return  int                                                     Number of items stored in the collection.
         */
        public function count()
        /**/
        {
            return count($this->collection);
        }

        /** Special iterator methods **/

        /**
         * Returns the current position of the iterator.
         *
         * @octdoc  m:iterator/getPosition
         * @return  int                                                     Current iterator position.
         */
        public function getPosition()
        /**/
        {
            return $this->position;
        }

        /**
         * Returns a copy of the data stored in collection.
         *
         * @octdoc  m:iterator/getArrayCopy
         * @return  array                                                   Data stored in collection.
         */
        public function getArrayCopy()
        /**/
        {
            return $this->collection->getArrayCopy();
        }
    }
}
