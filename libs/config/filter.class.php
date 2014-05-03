<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\config {
    /**
     * Implements FilterIterator for filtering configuration.
     *
     * @octdoc      c:config/filter
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class filter extends \FilterIterator 
    /**/
    {
        /**
         * Prefix to use as filter.
         *
         * @octdoc  p:filter/$prefix
         * @type    string
         */
        private $prefix = '';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:filter/__construct
         * @param   Iterator    $config     Config object to filter.
         * @param   string      $prefix     Prefix to filter for.
         */
        public function __construct(\org\octris\core\config $config, $prefix)
        /**/
        {
            $this->prefix = rtrim($prefix, '.');

            if (isset($config[$this->prefix])) {
                $tmp = new \ArrayIterator(\org\octris\core\type\collection::normalize($config[$this->prefix]));
            } else {
                $tmp = new \ArrayIterator();
            }
            
            parent::__construct($tmp);

            $this->rewind();
        }

        /**
         * Return key of current item.
         *
         * @octdoc  m:filter/key
         * @return  mixed               Key of current item.
         */
        public function key()
        /**/
        {
            return $this->prefix . '.' . parent::key();
        }

        /**
         * Get copy of filtered array.
         *
         * @octdoc  m:filter/getArrayCopy
         * @param   bool    $clean      Optional, default is FALSE. If TRUE the prefix will be removed from the keys.
         * @return  array               Filtered array.
         */
        public function getArrayCopy($clean = false)
        /**/
        {
            $this->rewind();

            $data = array();

            if ($clean) {
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
         * @octdoc  m:filter/accept
         * @return  bool        Returns TRUE, if element should be part of result.
         */
        public function accept()
        /**/
        {
            return true;
        }
    }
}
