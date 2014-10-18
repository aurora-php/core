<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\tpl {
    /**
     * Interface for implementing js/css compressors.
     *
     * @octdoc      i:tpl/compress_if
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface compress_if
    /**/
    {
        /**
         * Execute compressor.
         *
         * @octdoc  m:compress/exec
         * @param   array       $files      Files to compress.
         * @param   string      $out        Name of path to store generated file in.
         * @param   string      $inp        Name of base-path to lookup source file in.
         * @param   string      $type       Type of files to compress.
         * @return  string                  Name of generated file.
         */
        public function exec($files, $out, $inp, $type);
        /**/
    }
}
