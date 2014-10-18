<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\db\type {
    /**
     * Iterator for recursive iterating data objects of query results
     *
     * @octdoc      c:db/dataiterator
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class dataiterator implements \Iterator
    /**/
    {
        /**
         * The dataobject to iterate.
         *
         * @octdoc  p:dataiterator/$data
         * @type    \octris\core\db\type\subobject
         */
        protected $data;
        /**/

        /**
         * Keys stored in dataobject.
         *
         * @octdoc  p:dataiterator/$keys
         * @type    array
         */
        protected $keys;
        /**/

        /**
         * Internal pointer position.
         *
         * @octdoc  p:dataiterator/$position
         * @type    int
         */
        protected $position = 0;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:dataiterator/__construct
         * @parem   \octris\core\db\type\subobject    $dataobject         The dataobject to iterate.
         */
        public function __construct(\octris\core\db\type\subobject $dataobject)
        /**/
        {
            $this->data = $dataobject;          
            $this->keys = $dataobject->getKeys();
        }

        /** Iterator **/

        /**
         * Get value of item.
         *
         * @octdoc  m:dataiterator/current
         * @return  mixed                                                               Value stored at current position.
         */
        public function current()
        /**/
        {
            return $this->data[$this->keys[$this->position]];
        }

        /**
         * Get key of current item.
         *
         * @octdoc  m:dataiterator/key
         * @return  scalar                                                              Key of current position.
         */
        public function key()
        /**/
        {
            return $this->keys[$this->position];
        }

        /**
         * Advance pointer.
         *
         * @octdoc  m:dataiterator/next
         */
        public function next()
        /**/
        {
            ++$this->position;
        }

        /**
         * Reset pointer.
         *
         * @octdoc  m:dataiterator/rewind
         */
        public function rewind()
        /**/
        {
            $this->position = 0;
        }

        /**
         * Test if current pointer position is valid.
         *
         * @octdoc  m:dataiterator/valid
         * @return  bool                                                                True, if position is valid.
         */
        public function valid()
        /**/
        {
            return isset($this->keys[$this->position]);
        }
    }
}
