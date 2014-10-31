<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Validate\Type;

/**
 * Validator for octris project names. A qualified project name is:
 *
 * <vendor-name>/<module-name>
 *
 * e.g.: octris/core
 *
 * The string must match the pattern:
 *
 * [A-Za-z0-9][A-Za-z0-9_]*\/[A-Za-z0-9][A-Za-z0-9_]*
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Project extends \Octris\Core\Validate\Type
{
    /**
     * Validator implementation.
     *
     * @param   mixed       $value          Value to validate.
     * @return  bool                        Returns true if value is valid.
     */
    public function validate($value)
    {
        return !!preg_match(
            '/^[A-Za-z0-9][A-Za-z0-9_]*\/[A-Za-z0-9][A-Za-z0-9_]*$/',
            $value
        );
    }
}
