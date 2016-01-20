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
 * Data provider.
 *
 * @copyright   copyright (c) 2011-2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Provider
{
    /**
     * Provider instances.
     *
     * @type    array
     */
    protected static $instances = array();

    /**
     * Internal data storage
     *
     * @type    array
     */
    protected static $storage = array();

    /**
     * Data validators
     *
     * @type    array
     */
    protected $validators = array();

    /**
     * Stores validation flags and sanitized values.
     *
     * @type    array
     */
    protected $validated = array();

    /**
     * Stores name of data that is granted access to by instance.
     *
     * @type    string
     */
    protected $name = null;

    /**
     * Constructor.
     *
     * @param   string          $name               Name of data to grant access to.
     */
    protected function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Debug information.
     */
    public function __debugInfo()
    {
        return array(
            'storage'    => self::$storage[$this->name],
            'validated'  => $this->validated
        );
    }

    /**
     * Returns a new instance of the data provider by granting access to
     * data stored with the specified name in the data provider.
     *
     * @param   string                          $name               Name of data to access.
     * @return  \Octris\Core\Provider                           Instance of data provider.
     */
    public static function access($name)
    {
        $name = strtolower($name);

        if (!isset(self::$instances[$name])) {
            if (!isset(self::$storage[$name])) {
                throw new \Exception("cannot access unknown data '$name'");
            } else {
                self::$instances[$name] = new static($name);
            }
        }

        return self::$instances[$name];
    }

    /**
     * Save data in provider.
     *
     * @param   string          $name               Name to store data as.
     * @param   array           $data               Data to store.
     * @param   \ArrayObject    $storage            Optional external storage to configure for data provider.
     */
    public static function set($name, array $data, \ArrayObject $storage = null)
    {
        $name = strtolower($name);

        if (!is_null($storage)) {
            $storage->exchangeArray($data);

            self::$storage[$name] = array(
                'data'  => $storage
            );
        } else {
            self::$storage[$name] = array(
                'data'  => $data
            );
        }
    }

    /**
     * Validate provider data without first adding a validation schema.
     *
     * @param   array           $schema             Validation schema.
     */
    public function doValidate(array $schema)
    {
        $schema = new \Octris\Core\Validate\Schema($schema);
        $valid  = !!$schema->validate(self::$storage[$this->name]['data']);
        $errors = $schema->getErrors();

        foreach (self::$storage[$this->name]['data'] as $key => $value) {
            $this->validated[$key] = array(
                'value'    => $value,
                'is_valid' => true
            );
        }

        $return = array(
            (count($errors) == 0),
            $schema->getData(),
            $errors,
            $schema                                                 // validator instance
        );

        return $return;
    }

    /**
     * Add a validation schema.
     *
     * @param   string          $name               Name of validator.
     * @param   array           $schema             Validation schema.
     */
    public function addValidator($name, array $schema)
    {
        $this->validators[$name] = function () use ($schema) {
            static $return = null;

            if (is_null($return)) {
                $return = $this->doValidate($schema);
            }

            return $return;
        };
    }

    /**
     * Test if a specified validator is available.
     *
     * @param   string          $name               Name of validator.
     * @return  bool                                Returns true, if validator is available.
     */
    public function hasValidator($name)
    {
        return (isset($this->validators[$name]));
    }

    /**
     * Returns (validated and sanitized) stored data validated with specified
     * validator name.
     *
     * @param   string          $name               Name of validator to apply.
     * @return  array                               Validated and sanitized data.
     */
    public function applyValidator($name)
    {
        if (!isset($this->validators[$name])) {
            throw new \Exception("unknown validator '$name'");
        }

        $return = $this->validators[$name]();

        return $return;
    }

    /**
     * Test if the data field of the specified name is available.
     *
     * @param   string          $name               Name of data field to test.
     * @return  bool                                Returns true if data field is available.
     */
    public function isExist($name)
    {
        return (isset(self::$storage[$this->name]['data'][$name]));
    }

    /**
     * Validate a stored data field with specified validator type and options.
     *
     * @param   string                                  $name           Name of data field to validate.
     * @param   string|\Octris\Core\Validate\Type       $validator      Validation type or validator instance.
     * @param   array                                   $options        Optional settings for validation.
     * @return  bool                                                    Returns true if validation succeeded.
     */
    public function isValid($name, $validator, array $options = array())
    {
        $key = $name;

        if (!isset($this->validated[$key])) {
            if (is_scalar($validator) && class_exists($validator) && is_subclass_of($validator, '\Octris\Core\Validate\Type')) {
                $validator = new $validator($options);
            }

            if (!($validator instanceof \Octris\Core\Validate\Type)) {
                throw new \Exception(sprintf("'%s' is not a validation type", get_class($validator)));
            }

            if (($is_valid = isset(self::$storage[$this->name]['data'][$name]))) {
                $value = self::$storage[$this->name]['data'][$name];
                $value = $validator->preFilter($value);

                $this->validated[$key] = array(
                    'value'     => $value,
                    'is_valid'  => $validator->validate($value)
                );
            } else {
                $this->validated[$key] = array(
                    'value'     => null,
                    'is_valid'  => false
                );
            }
        }

        return $this->validated[$key]['is_valid'];
    }

    /**
     * Validates a specified data field and returns it, if it's valid.
     *
     * @param   string                                  $name           Name of data field to validate.
     * @return  mixed                                                   Returns value or null if field was not validated.
     */
    public function getValue($name)
    {
        $return = null;

        if (!isset($this->validated[$name])) {
            trigger_error("'%s' has not been validated", $name);
        } else {
            $return = $this->validated[$name]['value'];
        }

        return $return;
    }

    /**
     * Filter provider for prefix.
     *
     * @param   string                              $prefix     Prefix to use for filter.
     * @return  \Octris\Core\Provider\Filter                Filter iterator.
     */
    public function filter($prefix)
    {
        return new \Octris\Core\Provider\Filter(
            $prefix,
            array_keys(self::$storage[$this->name]['data'])
        );
    }

    /**
     * Set a specified data field with a value.
     *
     * @param   string                                          $name           Name of data field to set.
     * @param   mixed                                           $value          Value to set for data field.
     *                                                                          succeeded or null, if no validation type was specified
     */
    public function setValue($name, $value)
    {
        unset($this->validated[$name]); // Value must be validated before next getValue

        self::$storage[$this->name]['data'][$name] = $value;
    }

    /**
     * Purge data from provider.
     *
     * @param   string              $name               Name of data to purge.
     */
    public static function purge($name)
    {
        $name = strtolower($name);

        $instance = static::access($name);
        $instance->validated  = array();
        $instance->validators = array();

        unset($instance);
        unset(self::$storage[$name]);
    }
}
