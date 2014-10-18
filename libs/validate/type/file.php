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
     * Validator for testing if a string contains a valid (existing) file.
     *
     * @octdoc      c:type/file
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class file extends \org\octris\core\validate\type 
    /**/
    {
        /**
         * Validator implementation.
         *
         * @octdoc  m:file/validate
         * @param   mixed       $value          Value to validate.
         * @return  bool                        Returns true if value is valid.
         */
        public function validate($value)
        /**/
        {
            return (is_file($value));
        }
    }
}
