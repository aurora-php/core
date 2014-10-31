<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Provider;

/**
 * Implements FilterIterator for filtering provider properties.
 *
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 **
 */

class Filter extends \FilterIterator {
    /**
     * Prefix to use as filter.
     *
     * @type    string
     */
    private $prefix = '';
    
    /**
     * Constructor.
     *
     * @param   string          $prefix         Prefix to filter for.
     * @param   array           $keys           Array of property names.
     */
    public function __construct($prefix, array $keys)
    {
        parent::__construct(new \ArrayIterator($keys));

        $this->prefix = $prefix;
        $this->rewind();
    }

    /**
     * Filter implementation.
     *
     * @return  bool        Returns TRUE, if element should be part of result.
     */
    public function accept()
    {
        return (substr($this->key(), 0, strlen($this->prefix)) == $this->prefix);
    }
}
