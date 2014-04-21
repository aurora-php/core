<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\validate\type {
    /**
     * Validator for gender validation.
     *
     * @octdoc      c:type/gender
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class gender extends \org\octris\core\validate\type
    /**/
    {
        /**
         * Validation pattern.
         *
         * @octdoc  p:gender/$pattern
         * @type    string
         */
        protected $pattern = '/^[MF]$/';
        /**/
        
        /**
         * Validator implementation.
         *
         * @octdoc  m:gender/validate
         * @param   mixed       $value          Value to validate.
         * @return  bool                        Returns true if value is valid.
         */
        public function validate($value)
        /**/
        {
            preg_match($this->pattern, $value);
        }
    }
}

