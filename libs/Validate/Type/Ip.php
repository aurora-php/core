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
 * Validator for IP addresses.
 *
 * @copyright   copyright (c) 2015 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Ip extends \Octris\Core\Validate\Type
{
    /**
     * Filter flags.
     *
     * @type    int
     */
    const IPV4 = FILTER_FLAG_IPV4;
    const IPV6 = FILTER_FLAG_IPV6;
    const PRIVATE_RANGE = FILTER_FLAG_NO_PRIV_RANGE;
    const RESERVED_RANGE = FILTER_FLAG_NO_RES_RANGE;

    /**
     * Constructor.
     *
     * @param   array       $options        Optional options for validator.
     */
    public function __construct(array $options = array())
    {
        if (!isset($options['flags'])) {
            $options['flags'] = self::IPV4 | self::IPV6 | self::PRIVATE_RANGE | self::RESERVED_RANGE;
        }

        $this->options = $options;
    }

    /**
     * Validator implementation.
     *
     * @param   mixed       $value          Value to validate.
     * @return  bool                        Returns true if value is valid.
     */
    public function validate($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP, $this->options['flags']);
    }
}
