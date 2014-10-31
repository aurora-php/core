<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Tpl;

/**
 * Interface for implementing js/css compressors.
 *
 * @copyright   copyright (c) 2013-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface ICompress
{
    /**
     * Execute compressor.
     *
     * @param   array       $files      Files to compress.
     * @param   string      $out        Name of path to store generated file in.
     * @param   string      $inp        Name of base-path to lookup source file in.
     * @param   string      $type       Type of files to compress.
     * @return  string                  Name of generated file.
     */
    public function exec($files, $out, $inp, $type);
    /**/
}
