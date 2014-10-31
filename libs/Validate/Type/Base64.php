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
 * Validator for base64 encoded data. Supports standard base64 and base64
 * for URLs format.
 *
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Base64 extends \Octris\Core\Validate\Type
{
    /**
     * Validator implementation.
     *
     * @param   mixed       $value          Value to validate.
     * @return  bool                        Returns true if value is valid.
     */
    public function validate($value)
    {
        $value = preg_replace('/\s/', '', $value);

        return (strlen($value) > 1 &&
                (preg_match(
                    '/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/',
                    $value
                ) || preg_match(
                    '/^(?:[A-Za-z0-9-_]{4})*(?:[A-Za-z0-9-_]{2,3})?$/',
                    $value
                )));
    }
}
