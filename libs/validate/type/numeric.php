<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\validate\type;

/**
 * Validator for values containing numeric value.
 *
 * @octdoc      c:type/numeric
 * @copyright   copyright (c) 2013 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class numeric extends \octris\core\validate\type\digit
{
    /**
     * Validation pattern.
     *
     * @octdoc  p:numeric/$pattern
     * @type    string
     */
    protected $pattern = '/^[+-]?[0-9]+(\.[0-9]+)$/';
    /**/
}
