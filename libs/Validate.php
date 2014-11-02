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
     */
    const T_OBJECT = 1;
    const T_ARRAY  = 2;

    /**
     * Available validation types.
     *
     *          T_FILE, T_GENDER, T_MULTILINE, T_NUMERIC, T_PATH, T_PATTERN, T_PRINTABLE,
     *          T_PROJECT, T_UTF8, T_XDIGIT
     */
    const T_ALPHA     = '\Octris\Core\Validate\Type\Alpha';
    const T_ALPHANUM  = '\Octris\Core\Validate\Type\Alphanum';
    const T_BASE64    = '\Octris\Core\Validate\Type\Base64';
    const T_BOOL      = '\Octris\Core\Validate\Type\Bool';
    const T_DIGIT     = '\Octris\Core\Validate\Type\Digit';
    const T_EMAIL     = '\Octris\Core\Validate\Type\Email';
    const T_FILE      = '\Octris\Core\Validate\Type\File';
    const T_GENDER    = '\Octris\Core\Validate\Type\Gender';
    const T_MULTILINE = '\Octris\Core\Validate\Type\Multiline';
    const T_NUMERIC   = '\Octris\Core\Validate\Type\Numeric';
    const T_PATH      = '\Octris\Core\Validate\Type\Path';
    const T_PATTERN   = '\Octris\Core\Validate\Type\Pattern';
    const T_PRINTABLE = '\Octris\Core\Validate\Type\Printable';
    const T_PROJECT   = '\Octris\Core\Validate\Type\Project';
    const T_URL       = '\Octris\Core\Validate\Type\Url';
    const T_XDIGIT    = '\Octris\Core\Validate\Type\Xdigit';

    /**
     * Validation types which are directly implemented in schema validator.
     *
     * @type    int
     */
    const T_CALLBACK = 3;
    const T_CHAIN    = 4;

    /**
     * Protected constructor and magic clone method to prevent existance of multiple instances.
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
