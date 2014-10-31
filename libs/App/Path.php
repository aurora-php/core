<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\App;

/**
 * Application path object.
 *
 * @copyright   copyright (c) 2013 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Path
{
    /**
     * Unnormalized path.
     *
     * @type    string
     */
    protected $path = '';
    
    /**
     * Constructor.
     *
     * @param   string          $type               The type of the path to return.
     * @param   string          $module             Optional name of module to return path for.
     *                                              Default is: current application name.
     * @param   string          $rel_path           Optional additional relative path to add.
     */
    public function __construct($type, $module = '', $rel_path = '')
    {
        $reg = \Octris\Core\Registry::getInstance();

        if ($type == \Octris\Core\App::T_PATH_HOME_ETC) {
            $info = posix_getpwuid(posix_getuid());
            $base = $info['dir'];
        } else {
            $base = $reg->OCTRIS_BASE;
        }

        $this->path = sprintf(
            $type,
            $base,
            ($module
                ? $module
                : $reg->OCTRIS_APP)
        ) . ($rel_path
                ? '/' . $rel_path
                : '');
    }

    /**
     * Return path stored in class instance.
     *
     * @return  string                              Path.
     */
    public function __toString()
    {
        return $this->path;
    }

    /**
     * Check if the path defined by the class instance exists.
     *
     * @return  bool                                Returns true if the path exists, returns
     *                                              false if the path does not exist.
     */
    public function exists()
    {
        return (file_exists($this->path) && is_dir($this->path));
    }

    /**
     * Normalize the path.
     *
     * @return  string                              Normalized path.
     */
    public function normalize()
    {
        if (substr($path, 0, 1) != '/') {
            $path = getcwd() . '/' . $path;
        }

        $parts = array_filter(explode('/'), $path, 'strlen');
        $path  = array();

        foreach ($parts as $part) {
            if ($part == '..') {
                array_pop($path);
            } elseif ($part != '.') {
                $path[] = $part;
            }
        }

        $path = implode('/', $path);

        if (file_exists($path) && linkinfo($path) > 0) {
            $path = readlink($path);
        }

        return $path;
    }

    /**
     * Create the path.
     *
     * @param   string          $mode               Optional mode for new path (default: 0755).
     * @return  bool                                Returns true if the creation of the path succeeded.
     */
    public function create($mode = 0755)
    {
        return mkdir($this->path, $mode, true);
    }
}
