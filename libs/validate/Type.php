<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Validate;

/**
 * Superclass for validator types.
 *
 * @octdoc      c:validate/type
 * @copyright   copyright (c) 2010-2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class Type
{
    /**
     * Stores validation options.
     *
     * @octdoc  p:type/$options
     * @type    array
     */
    protected $options = array();
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:type/__construct
     * @param   array       $options        Optional options for validator.
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * Validator implementation.
     *
     * @octdoc  m:type/validate
     * @param   mixed       $value          Value to validate.
     * @return  bool                        Returns true if value is valid.
     * @abstract
     */
    abstract public function validate($value);
    /**/

    /**
     * Filter values for unwanted characters before validating them.
     *
     * @octdoc  m:type/preFilter
     * @param   mixed       $value          Value to filter.
     * @return  mixed                       Filtered value.
     */
    public function preFilter($value)
    {
        // replace nullbytes
        $value = str_replace("\0", '', $value);

        return $value;
    }

    /**
     * Return possible set options.
     *
     * @octdoc  m:type/getOptions
     * @return  array                       Validator options.
     */
    protected function getOptions()
    {
        return $this->options;
    }
}
