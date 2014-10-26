<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\db\type;

/**
 * Iterator for recursive iterating data objects of query results
 *
 * @octdoc      c:db/recursivedataiterator
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class recursivedataiterator extends \octris\core\db\type\dataiterator implements \RecursiveIterator
{
    /**
     * Constructor.
     *
     * @octdoc  m:recursivedataiterator/__construct
     * @parem   \octris\core\db\type\subobject    $dataobject         The dataobject to iterate.
     */
    public function __construct(\octris\core\db\type\subobject $dataobject)
    {
        parent::__construct($dataobject);
    }

    /** RecursiveIterator **/

    /**
     * Returns an iterator for the current item.
     *
     * @octdoc  m:recursivedataiterator/getChildren
     * @return  \octris\core\db\type\recursivedataiterator          Recursive data iterator for item.
     */
    public function getChildren()
    {
        return new static($this->data[$this->keys[$this->position]]);
    }
    
    /**
     * Returns if an iterator can be created fot the current item.
     *
     * @octdoc  m:recursivedataiterator/hasChildren
     * @return  bool                                                    Returns true if an iterator can be
     *                                                                  created for the current item.
     */
    public function hasChildren()
    {
        $item = $this->data[$this->keys[$this->position]];
        
        return (is_object($item) && $item instanceof \octris\core\db\type\subobject);
    }
}

