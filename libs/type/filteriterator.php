<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\type {
    /**
     * Enhances the PHP SPL FilterIterator in that it accepts a closure in constructer, which will be used as filter.
     *
     * @octdoc      c:type/filteriterator
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class filteriterator extends \FilterIterator
    /**/
    {
        /**
         * The filter to apply.
         *
         * @octdoc  p:filteriterator/$filter
         * @type    callable
         */
        protected $filter;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:filteriterator/__construct
         * @param   \Iterator                   $iterator                       The iterator to filter.
         * @param   callable                    $filter                         The closure that implements the filter.
         */
        public function __construct(\Iterator $iterator, callable $filter)
        /**/
        {
            parent::__construct($iterator);

            $this->filter = $filter;
        }

        /**
         * Returns false, if item should be filtered from output.
         *
         * @octdoc  m:filteriterator/accept
         * @return  bool                                                        Whether the item should be output or not.
         */
        public function accept()
        /**/
        {
            $cb = $this->filter;

            return $cb($this->current(), $this->key());
        }
    }
}
