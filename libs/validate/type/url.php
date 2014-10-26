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
 * Validator for testing if a string is a valid URL.
 *
 * @octdoc      c:type/url
 * @copyright   copyright (c) 2010-2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class url extends \octris\core\validate\type 
{
    /**
     * Validation pattern.
     *
     * @octdoc  p:url/$pattern
     * @type    string
     */
    protected $pattern = "/^%s:\/\/(([a-z0-9\$\-\_\.\+\!\*'\(\)\,\;\?\&\=]|(\%[0-9a-f]{2}))+(\:([a-z0-9\$\-\_\.\+\!\*'\(\)\,\;\?\&\=]|(\%[0-9a-f]{2}))+)?\@)?((([a-z0-9]|([a-z0-9]([a-z0-9\-])*[a-z0-9]))\.)*([a-z]|([a-z][a-z0-9\-]*[a-z0-9]))|[0-9]{1,3}(\.[0-9]{1,3}){3})(\:[0-9]+)?(\/([a-z0-9\$\-\_\.\+\!\*'\(\)\,\;\:\@\&\=]|(\%[0-9a-f]{2}))*(\/([a-z0-9\$\-\_\.\+\!\*'\(\)\,\;\:\@\&\=]|(\%[0-9a-f]{2}))*)*(\?([a-z0-9\$\-\_\.\+\!\*'\(\)\,\;\:\@\&\=]|(\%[0-9a-f]{2}))*)?)?$/i";
    /**/

    /**
     * Default scheme to use, if no scheme is provided.
     *
     * @octdoc  p:url/$default_scheme
     * @type    string
     */
    protected $default_scheme = 'http://';
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:url/__construct
     * @param   array       $options        Optional options for validator.
     */
    public function __construct(array $options = array())
    {
        if (!isset($options['schemes']) || !is_array($options['schemes'])) {
            $options['schemes'] = array('http', 'https');
        }
        
        if (isset($options['default_scheme']) && is_string($options['default_scheme'])) {
            $this->default_scheme = $options['default_scheme'];
        }
        
        parent::__construct($options);
    }

    /**
     * Overwrite preFilter of superclass to add a default scheme, if no scheme is specified.
     *
     * @octdoc  m:url/preFilter
     * @param   string      $value      The provided URL.
     */
    public function preFilter($value)
    {
        $value = parent::preFilter($value);

        if (trim($value) != '' && !preg_match('|^[^:]+://|', $value)) {
            $value = $this->default_scheme . $value;
        }

        return $value;
    }

    /**
     * Validator implementation.
     *
     * @octdoc  m:url/validate
     * @param   mixed       $value          Value to validate.
     * @return  bool                        Returns true if value is valid.
     */
    public function validate($value)
    {
        $pattern = sprintf(
            $this->pattern,
            (count($options['schemes']) > 0
             ? '(' . implode('|', $options['scheme']) . ')'
             : '(.+)')
        );
                    
        return preg_match($pattern, $value);
    }
}

