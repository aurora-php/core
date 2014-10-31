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
 * Link reference.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 *
 * @todo        Allow cross-device links (riak -> mysql, etc.)?
 */
class Dbref
{
    /**
     * Name of collection to reference to.
     *
     * @type    string
     */
    protected $collection;
    
    /**
     * Key to reference to.
     *
     * @type    string
     */
    protected $key;
    
    /**
     * Constructor.
     *
     * @param   string          $collection         Name of collection to link to.
     * @param   string          $key                Key in bucket to link to.
     */
    public function __construct($collection, $key)
    {
        $this->collection = $collection;
        $this->key        = $key;
    }

    /**
     * Return reference property.
     *
     * @param   string          $name               Name of property to return value of.
     */
    public function __get($name)
    {
        return (isset($this->{$name})
                ? $this->{$name}
                : null);
    }
}
