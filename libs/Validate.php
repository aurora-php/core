<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core;

/**
 * Validation base class.
 *
 * octdoc       c:core/validate
 * @copyright   copyright (c) 2010-2014 by Harald Lapp
 * @author      Harald Lapp <harald.lapp@gmail.com>
 */
class Validate
{
    /**
     * Schema structure types.
     *
     */
    const T_OBJECT = 1;
    const T_ARRAY  = 2;
    
    /**
     * Available validation types.
     *
     *          T_FILE, T_GENDER, T_MULTILINE, T_NUMERIC, T_PATH, T_PATTERN, T_PRINTABLE,
     *          T_PROJECT, T_UTF8, T_XDIGIT
     */
    const T_ALPHA     = '\octris\core\validate\type\alpha';
    const T_ALPHANUM  = '\octris\core\validate\type\alphanum';
    const T_BASE64    = '\octris\core\validate\type\base64';
    const T_BOOL      = '\octris\core\validate\type\bool';
    const T_DIGIT     = '\octris\core\validate\type\digit';
    const T_EMAIL     = '\octris\core\validate\type\email';
    const T_FILE      = '\octris\core\validate\type\file';
    const T_GENDER    = '\octris\core\validate\type\gender';
    const T_MULTILINE = '\octris\core\validate\type\multiline';
    const T_NUMERIC   = '\octris\core\validate\type\numeric';
    const T_PATH      = '\octris\core\validate\type\path';
    const T_PATTERN   = '\octris\core\validate\type\pattern';
    const T_PRINTABLE = '\octris\core\validate\type\printable';
    const T_PROJECT   = '\octris\core\validate\type\project';
    const T_URL       = '\octris\core\validate\type\url';
    const T_XDIGIT    = '\octris\core\validate\type\xdigit';
    
    /**
     * Validation types which are directly implemented in schema validator.
     *
     * @type    int
     */
    const T_CALLBACK = 3;
    const T_CHAIN    = 4;
    
    /**
     * Protected constructor and magic clone method to prevent existance of multiple instances.
     *
     */
    protected function __construct()
    {
    }
    protected function __clone()
    {
    }
    
    /**
     * Test a value if it validates to the specified schema.
     *
     * @param   mixed           $value              Value to test.
     * @param   array           $schema             Validation schema.
     * @param   int             $mode               Optional validation mode.
     * @return  mixed                               Returns true, if valid otherwise an array with error messages.
     */
    public static function validate($value, array $schema, $mode = \Octris\Core\Validate\Schema::T_STRICT)
    {
        $instance = new \Octris\Core\Validate\Schema($schema, $mode);
        $is_valid = $instance->validate($value);

        return ($is_valid === true
                ? $is_valid
                : $instance->getErrors());
    }
}
