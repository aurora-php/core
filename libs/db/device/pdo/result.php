<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\db\device\pdo {
    /**
     * Query result object.
     *
     * @octdoc      c:pdo/result
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     * @todo        Enable scrollable cursor for databases which support it.
     */
    class result implements \Iterator, \Countable
    /**/
    {
        /**
         * Instance of \PDOStatement
         *
         * @octdoc  p:result/$statement
         * @type    \PDOStatement
         */
        protected $statement;
        /**/

        /**
         * Cursor position.
         *
         * @octdoc  p:result/$position
         * @type    int
         */
        protected $position = -1;
        /**/

        /**
         * Cache for rewinding cursor.
         *
         * @octdoc  p:result/$cache
         * @type    array
         */
        protected $cache = array();
        /**/

        /**
         * Valid result row.
         *
         * @octdoc  p:result/$valid
         * @type    bool
         */
        protected $valid;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:result/__construct
         * @param   \PDOStatement           $statement          PDO statement object.
         */
        public function __construct(\PDOStatement $statement)
        /**/
        {
            $this->statement = $statement;
            
            $this->next();
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
            return $this->statement->rowCount();
        }

        /**
         * Return current item of the search result.
         *
         * @octdoc  m:result/current
         * @return  array                                       Row data.
         */
        public function current()
        /**/
        {
            return ($this->valid
                    ? $this->cache[$this->position]
                    : false);
        }

        /**
         * Advance cursor to the next item.
         *
         * @octdoc  m:result/next
         */
        public function next()
        /**/
        {
            if (!($this->valid = isset($this->cache[++$this->position]))) {
                if (($this->valid = !!($row = $this->statement->fetch(\PDO::FETCH_OBJ)))) {
                    $this->cache[$this->position] = $row;
                }
            }
        }

        /**
         * Returns the cursor position.
         *
         * @octdoc  m:result/key
         * @return  int                                      Cursor position.
         */
        public function key()
        /**/
        {
            return $this->position;
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
            return $this->valid;
        }
    }
}
