<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * Validation base class.
     *
     * octdoc       c:core/validate
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald.lapp@gmail.com>
     */
    class validate
    /**/
    {
        /**
         * Schema structure types.
         *
         * @octdoc  d:validate/T_OBJECT, T_ARRAY
         */
        const T_OBJECT = 1;
        const T_ARRAY  = 2;
        /**/

        /**
         * Available validation types.
         *
         * @octdoc  d:validate/T_ALPHA, T_ALPHANUM, T_BASE64, T_BOOL, T_DIGIT, T_EMAIL, 
         *          T_FILE, T_MULTILINE, T_NUMERIC, T_PATH, T_PATTERN, T_PRINTABLE, 
         *          T_PROJECT, T_UTF8, T_XDIGIT
         */
        const T_ALPHA     = '\org\octris\core\validate\type\alpha';
        const T_ALPHANUM  = '\org\octris\core\validate\type\alphanum';
        const T_BASE64    = '\org\octris\core\validate\type\base64';
        const T_BOOL      = '\org\octris\core\validate\type\bool';
        const T_DIGIT     = '\org\octris\core\validate\type\digit';
        const T_EMAIL     = '\org\octris\core\validate\type\email';
        const T_FILE      = '\org\octris\core\validate\type\file';
        const T_MULTILINE = '\org\octris\core\validate\type\multiline';
        const T_NUMERIC   = '\org\octris\core\validate\type\numeric';
        const T_PATH      = '\org\octris\core\validate\type\path';
        const T_PATTERN   = '\org\octris\core\validate\type\pattern';
        const T_PRINTABLE = '\org\octris\core\validate\type\printable';
        const T_PROJECT   = '\org\octris\core\validate\type\project';
        const T_URL       = '\org\octris\core\validate\type\url';
        const T_XDIGIT    = '\org\octris\core\validate\type\xdigit';
        /**/
        
        /**
         * Validation types which are directly implemented in schema validator.
         *
         * @octdoc  d:validate/T_CALLBACK, T_CHAIN
         * @type    int
         */
        const T_CALLBACK = 1;
        const T_CHAIN    = 2;
        /**/

        /**
         * Protected constructor and magic clone method to prevent existance of multiple instances.
         *
         * @octdoc  m:validate/__construct, __clone
         */
        protected function __construct() {}
        protected function __clone() {}
        /**/

        /**
         * Test a value if it validates to the specified schema.
         *
         * @octdoc  m:validate/test
         * @param   mixed           $value              Value to test.
         * @param   array           $schema             Validation schema.
         * @param   int             $mode               Optional validation mode.
         * @return  mixed                               Returns true, if valid otherwise an array with error messages.
         */
        public static function validate($value, array $schema, $mode = \org\octris\core\validate\schema::T_STRICT)
        /**/
        {
            $instance = new \org\octris\core\validate\schema($schema, $mode);
            $is_valid = $instance->validate($value);

            return ($is_valid === true
                    ? $is_valid
                    : $instance->getErrors());
        }
    }
}
