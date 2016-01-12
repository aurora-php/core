<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Device\Pdo;

/**
 * Query result object.
 *
 * @copyright   copyright (c) 2014-2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 * @todo        Enable scrollable cursor for databases which support it.
 */
class Result implements \Octris\Core\Db\Device\IResult
{
    /**
     * Instance of \PDOStatement
     *
     * @type    \PDOStatement
     */
    protected $statement;

    /**
     * Cursor position.
     *
     * @type    int
     */
    protected $position = -1;

    /**
     * Cache for rewinding cursor.
     *
     * @type    array
     */
    protected $cache = array();

    /**
     * Valid result row.
     *
     * @type    bool
     */
    protected $valid;

    /**
     * Constructor.
     *
     * @param   \PDOStatement           $statement          PDO statement object.
     */
    public function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;

        $this->next();
    }

    /**
     * Count number of items in the result set.
     *
     * @return  int                                         Number of items in the result-set.
     */
    public function count()
    {
        return $this->statement->rowCount();
    }

    /**
     * Return current item of the search result.
     *
     * @return  array                                       Row data.
     */
    public function current()
    {
        return ($this->valid
                ? $this->cache[$this->position]
                : false);
    }

    /**
     * Advance cursor to the next item.
     */
    public function next()
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
     * @return  int                                      Cursor position.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Rewind cursor.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Tests if cursor position is valid.
     *
     * @return  bool                                        Returns true, if cursor position is valid.
     */
    public function valid()
    {
        return $this->valid;
    }
}
