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
 * Link reference.
 *
 * @octdoc      c:type/dbref
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 *
 * @todo        Allow cross-device links (riak -> mysql, etc.)?
 */
class dbref
{
    /**
     * Name of collection to reference to.
     *
     * @octdoc  p:dbref/$collection
     * @type    string
     */
    protected $collection;
    /**/

    /**
     * Key to reference to.
     *
     * @octdoc  p:dbref/$key
     * @type    string
     */
    protected $key;
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:dbref/__construct
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
     * @octdoc  m:dbref/__get
     * @param   string          $name               Name of property to return value of.
     */
    public function __get($name)
    {
        return (isset($this->{$name})
                ? $this->{$name}
                : null);
    }
}
