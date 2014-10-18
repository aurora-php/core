<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\provider {
    /**
     * Implements FilterIterator for filtering provider properties.
     *
     * @octdoc      c:config/filter
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     **
     */

    class filter extends \FilterIterator {
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
         * @param   string          $prefix         Prefix to filter for.
         * @param   array           $keys           Array of property names.
         */
        public function __construct($prefix, array $keys)
        /**/
        {
            parent::__construct(new \ArrayIterator($keys));

            $this->prefix = $prefix;
            $this->rewind();
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
            return (substr($this->key(), 0, strlen($this->prefix)) == $this->prefix);
        }
    }
}
