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
 * Validator for values containing only digits.
 *
 * @octdoc      c:type/digit
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class digit extends \octris\core\validate\type
{
    /**
     * Validation pattern.
     *
     * @octdoc  p:digit/$pattern
     * @type    string
     */
    protected $pattern = '/^[0-9]+$/';
    /**/

    /**
     * Validator implementation.
     *
     * @octdoc  m:digit/validate
     * @param   mixed       $value          Value to validate.
     * @return  bool                        Returns true if value is valid.
     */
    public function validate($value)
    {
        if (($return = preg_match($this->pattern, $value))) {
            $return = (isset($this->options['min'])
                        ? ($value >= $this->options['min'])
                        : true);

            $return = ($return
                        ? (isset($this->options['max'])
                            ? ($value <= $this->options['max'])
                            : true)
                        : false);
        }

        return $return;
    }
}
