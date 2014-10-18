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
     * Validator for octris project names. A qualified project name is:
     *
     * <TLD>.<COMPANY-NAME>.<MODULE-NAME>
     *
     * e.g.: com.clipdealer.core
     *
     * @octdoc      c:type/alpha
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class project extends \org\octris\core\validate\type
    /**/
    {
        /**
         * Validator implementation.
         *
         * @octdoc  m:project/validate
         * @param   mixed       $value          Value to validate.
         * @return  bool                        Returns true if value is valid.
         */
        public function validate($value)
        /**/
        {
            $value .= '.';
            
            return !!preg_match(
                '/^[a-z]{2,4}\.([a-z0-9]+([a-z0-9\-]*[a-z0-9]+|)\.){2}$/',
                $value
            );
        }
    }
}
