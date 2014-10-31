<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Config;

/**
 * Implements FilterIterator for filtering configuration.
 *
 * @copyright   copyright (c) 2010-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Filter extends \FilterIterator
{
    /**
     * Prefix to use as filter.
     *
     * @type    string
     */
    private $prefix = '';
    
    /**
     * Remove prefix from key.
     *
     * @type    bool
     */
    private $clean = true;
    
    /**
     * Constructor.
     *
     * @param   Iterator    $config     Config object to filter.
     * @param   string      $prefix     Prefix to filter for.
     * @param   bool        $clean      Optional remove prefix from key.
     */
    public function __construct(\Octris\Core\Config $config, $prefix, $clean = true)
    {
        $this->prefix = rtrim($prefix, '.');
        $this->clean  = $clean;

        if (isset($config[$this->prefix])) {
            $tmp = new \ArrayIterator(\Octris\Core\Type\Collection::normalize($config[$this->prefix]));
        } else {
            $tmp = new \ArrayIterator();
        }

        parent::__construct($tmp);

        $this->rewind();
    }

    /**
     * Return key of current item.
     *
     * @return  mixed                   Key of current item.
     */
    public function key()
    {
        return (!$this->clean
                ? $this->prefix . '.'
                : '') . parent::key();
    }

    /**
     * Get copy of filtered array.
     *
     * @return  array               Filtered array.
     */
    public function getArrayCopy()
    {
        $this->rewind();

        $data = array();

        if ($this->clean) {
            $data = iterator_to_array($this);
        } else {
            foreach ($this as $k => $v) {
                $data[$this->prefix . '.' . $k] = $v;
            }
        }

        $this->rewind();

        return $data;
    }

    /**
     * Filter implementation.
     *
     * @return  bool        Returns TRUE, if element should be part of result.
     */
    public function accept()
    {
        return true;
    }
}
