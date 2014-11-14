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

use \Octris\Core\App as app;
use \Octris\Core\Registry as registry;

/**
 * handles application configuration
 *
 * @copyright   (c) 2010-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 * @todo        other fileformats: json, ini, conf, xml ... loader?
 * @todo        remove duplicate code
 */
class Config extends \Octris\Core\Type\Collection
{
    /**
     * Name of configuration file.
     *
     * @type    string
     */
    protected $name = '';

    /**
     * Constructor.
     *
     * @param   string  $name       Name of configuration file.
     */
    public function __construct($name)
    {
        $this->name = $name;

        $data = self::load($name);

        parent::__construct($data);
    }

    /**
     * Sets defaults for configuration. Values are only set, if the keys of the values are not already available
     * in the configuration.
     *
     * @param   mixed       $value      Value(s) to set as default(s).
     */
    public function setDefaults($value)
    {
        if (($tmp = self::normalize($value, true)) === false) {
            throw new Exception('don\'t know how to handle parameter of type "' . gettype($value) . '"');
        } else {
            $data = $this->getArrayCopy();
            $data = array_replace_recursive(self::deflatten($value), $data);

            $this->exchangeArray($data);
        }
    }

    /**
     * Filter configuration for prefix.
     *
     * @param   string                              $prefix     Prefix to use for filter.
     * @return  \Octris\Core\Config\Filter                  Filter iterator.
     */
    public function filter($prefix)
    {
        return new \Octris\Core\Config\Filter($this, $prefix);
    }

    /**
     * Save configuration file to destination. if destination is not
     * specified, try to save in ~/.<OCTRIS_APP_VENDOR>/<OCTRIS_APP_NAME>/<name>.yml
     *
     * @param   string  $file       Optional destination to save configuration to.
     * @return  bool                Returns TRUE on success, otherwise FALSE.
     */
    public function save($file = '')
    {
        if ($file == '') {
            $registry = registry::getInstance();

            $path = \Octris\Core\Os::getHome() . '/.';
                    $registry->OCTRIS_APP_VENDOR . '/' .
                    $registry->OCTRIS_APP_NAME;

            $file = $path . '/' . $this->name . '.yml';
        } else {
            $path = dirname($file);
            $file = basename($file);
        }

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return file_put_contents($file, yaml_emit($this->getArrayCopy()));
    }

    /**
     * Test whether a configuration file exists.
     *
     * @param   string                              $name       Optional name of configuration file to look for.
     * @return  bool                                            Returns true if the configuration file exists.
     */
    public static function exists($name = 'config')
    {
        // initialization
        $registry = registry::getInstance();
        $return   = false;

        // tests
        do {
            $path = $registry->OCTRIS_APP_BASE . '/etc/';
            $file = $path . '/' . $name . '.yml';

            if (($return = (is_file($file) && is_readable($file)))) {
                break;
            }

            $file = $path . '/' . $name . '_local.yml';

            if (($return = (is_file($file) && is_readable($file)))) {
                break;
            }

            $path = \Octris\Core\Os::getHome() . '/.';
                    $registry->OCTRIS_APP_VENDOR . '/' .
                    $registry->OCTRIS_APP_NAME;

            $file = $path . '/' . $name . '.yml';

            if (($return = (is_file($file) && is_readable($file)))) {
                break;
            }
        } while (false);

        return $return;
    }

    /**
     * Create a configuration from a specified file. The configuration file will be stored in
     * ~/.<OCTRIS_APP_VENDOR>/<OCTRIS_APP_NAME>/<name>.yml.
     *
     * @param   string                              $file       File to load and create configuration object from.
     * @param   string                              $name       Optional name of configuration file to create.
     * @return  \Octris\Core\Config|bool                        Returns an instance of the config class if the
     *                                                          configuration file.
     *                                                          was created successful, otherwise 'false' is returned.
     * @todo    error handling
     */
    public static function create($file, $name = 'config')
    {
        $registry = registry::getInstance();

        $return = false;

        if (is_file($file) && (yaml_parse_file($file) !== false)) {
            $path = \Octris\Core\Os::getHome() . '/.';
                    $registry->OCTRIS_APP_VENDOR . '/' .
                    $registry->OCTRIS_APP_NAME;

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            copy($file, $path . '/' . $name . '.yml');

            $return = new static($name);
        }

        return $return;
    }

    /**
     * Load configuration file. The loader looks in the following places,
     * loads the configuration file and merges them in the specified lookup order:
     *
     * - <OCTRIS_APP_BASE>/etc/<name>.yml
     * - <OCTRIS_APP_BASE>/etc/<name>_local.yml
     * - ~/.<OCTRIS_APP_VENDOR>/<OCTRIS_APP_NAME>/<name>.yml
     *
     * whereat the configuration file name -- in this example 'config' -- may be overwritten by the first parameter.
     *
     * @param   string                                     $name       Optional name of configuration file to load.
     * @return  \Octris\Core\Type\Collection                           Contents of the configuration file.
     */
    private static function load($name = 'config')
    {
        $registry = registry::getInstance();

        // initialization
        $cfg = array();

        // load default config file
        $path = $registry->OCTRIS_APP_BASE . '/etc/';
        $file = $path . '/' . $name . '.yml';

        if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
            $cfg = array_replace_recursive($cfg, $tmp);
        }

        // load local config file
        $file = $path . '/' . $name . '_local.yml';

        if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
            $cfg = array_replace_recursive($cfg, $tmp);
        }

        // load global framework configuration
        $path = \Octris\Core\Os::getHome() . '/.' .
                $registry->OCTRIS_APP_VENDOR . '/' .
                $registry->OCTRIS_APP_NAME;
        $file = $path . '/' . $name . '.yml';

        if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
            $cfg = array_replace_recursive($cfg, $tmp);
        }

        return new \Octris\Core\Type\Collection($cfg);
    }
}
