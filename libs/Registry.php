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
 * Implementation of a central registry uses DI container as storage
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald.lapp@gmail.com>
 */
class Registry extends \Octris\Core\Type\Container
{
    /**
     * Stores instance of registry object.
     *
     * @type    \octris\core\registry
     */
    private static $instance = null;
    
    /**
     * Constructor is protected to prevent instanciating registry.
     *
     */
    protected function __construct()
    {
    }

    /**
     * Clone is private to prevent multipleinstances of registry.
     *
     */
    private function __clone()
    {
    }

    /**
     * Return instance of registry.
     *
     * @return  \octris\core\registry           instance of registry
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }
}
