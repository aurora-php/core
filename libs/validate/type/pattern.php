<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\validate\type {
    /**
     * Validator for testing if a string matches a custom regular expression pattern.
     *
     * @octdoc      c:type/pattern
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class pattern extends \octris\core\validate\type 
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:pattern/__construct
         * @param   array       $options        Options required by validator.
         */
        public function __construct(array $options)
        /**/
        {
            if (!isset($options['pattern'])) {
                throw new \Exception('no pattern provided');
            }
            
            parent::__construct($options);
        }

        /**
         * Validator implementation.
         *
         * @octdoc  m:pattern/validate
         * @param   mixed       $value          Value to validate.
         * @return  bool                        Returns true if value is valid.
         */
        public function validate($value)
        /**/
        {
            return preg_match($this->options['pattern'], $value);
        }
    }
}
