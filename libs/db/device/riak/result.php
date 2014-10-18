<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\riak {
    /**
     * Query result object.
     *
     * @octdoc      c:riak/result
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class result implements \Iterator, \Countable
    /**/
    {
        /**
         * Device the result belongs to.
         *
         * @octdoc  p:result/$device
         * @type    \org\octris\core\db\device\riak
         */
        protected $device;
        /**/

        /**
         * Name of collection the result belongs to.
         *
         * @octdoc  p:result/$collection
         * @type    string
         */
        protected $collection;
        /**/

        /**
         * Array of result.
         *
         * @octdoc  p:result/$result
         * @type    array
         */
        protected $result = array();
        /**/

        /**
         * Current position in array.
         *
         * @octdoc  p:result/$position
         * @type    int
         */
        protected $position = 0;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:result/__construct
         * @param   \org\octris\core\db\device\riak     $device         Device the connection belongs to.
         * @param   string                              $collection     Name of collection the result belongs to.
         * @param   array                               $result         Query result.
         */
        public function __construct(\org\octris\core\db\device\riak $device, $collection, $result)
        /**/
        {
            $this->device     = $device;
            $this->collection = $collection;
            $this->result     = $result['response']['docs'];
        }

        /**
         * Count number of items in the result set.
         *
         * @octdoc  m:result/count
         * @return  int                                         Number of items in the result-set.
         */
        public function count()
        /**/
        {
            return count($this->result);
        }

        /**
         * Return current item of the search result.
         *
         * @octdoc  m:result/current
         * @return  \org\octris\core\db\device\riak\dataobject|bool  Returns either a dataobject with the stored contents of the current item or false, if the cursor position is invalid.
         */
        public function current()
        /**/
        {
            if (!$this->valid()) {
                $return = null;
            } else {
                $data = $this->result[$this->position]['fields'];
                $data['_id'] = $this->result[$this->position]['id'];

                $return = new \org\octris\core\db\device\riak\dataobject(
                    $this->device, 
                    $this->collection,
                    $data
                );
            }

            return $return;
        }

        /**
         * Advance cursor to the next item.
         *
         * @octdoc  m:result/next
         */
        public function next()
        /**/
        {
            ++$this->position;
        }

        /**
         * Returns the object-ID of the current search result item.
         *
         * @octdoc  m:result/key
         * @return  string                                      Object-ID.
         */
        public function key()
        /**/
        {
            $this->result[$this->position]['id'];
        }

        /**
         * Rewind cursor.
         *
         * @octdoc  m:result/rewind
         */
        public function rewind()
        /**/
        {
            $this->position = 0;
        }

        /**
         * Tests if cursor position is valid.
         *
         * @octdoc  m:result/valid
         * @return  bool                                        Returns true, if cursor position is valid.
         */
        public function valid()
        /**/
        {
            return array_key_exists($this->position, $this->result);
        }
    }
}
