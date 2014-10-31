<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Type;

/**
 * Iterator for recursive iterating data objects of query results
 *
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Recursivedataiterator extends \Octris\Core\Db\Type\Dataiterator implements \RecursiveIterator
{
    /**
     * Constructor.
     *
     * @parem   \Octris\Core\Db\Type\Subobject    $dataobject         The dataobject to iterate.
     */
    public function __construct(\Octris\Core\Db\Type\Subobject $dataobject)
    {
        parent::__construct($dataobject);
    }

    /** RecursiveIterator **/

    /**
     * Returns an iterator for the current item.
     *
     * @return  \octris\core\db\type\recursivedataiterator          Recursive data iterator for item.
     */
    public function getChildren()
    {
        return new static($this->data[$this->keys[$this->position]]);
    }

    /**
     * Returns if an iterator can be created fot the current item.
     *
     * @return  bool                                                    Returns true if an iterator can be
     *                                                                  created for the current item.
     */
    public function hasChildren()
    {
        $item = $this->data[$this->keys[$this->position]];

        return (is_object($item) && $item instanceof \octris\core\db\type\subobject);
    }
}
