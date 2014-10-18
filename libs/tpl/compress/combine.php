<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\tpl\compress {
    /**
     * Only combine files without compression.
     *
     * @octdoc      c:compress/combine
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class combine implements \org\octris\core\tpl\compress_if
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:combine/__construct
         */
        public function __construct()
        /**/
        {
        }
        
        /**
         * Execute combine.
         *
         * @octdoc  m:combine/exec
         * @param   array       $files      Files to compress.
         * @param   string      $out        Name of path to store file in.
         * @param   string      $inp        Name of base-path to lookup source file in.
         * @param   string      $type       Type of files to compress.
         * @return  string                  Name of created file.
         */
        public function exec($files, $out, $inp, $type)
        /**/
        {
            array_walk($files, function(&$file) use ($inp) {
                $file = escapeshellarg($inp . '/' . $file);
            });

            $tmp = tempnam('/tmp', 'oct');
            
            $cmd = sprintf(
                'cat %s > %s 2>&1',
                implode(' ', $files),
                $tmp
            );

            $ret = array(); $ret_val = 0;
            exec($cmd, $ret, $ret_val);

            $md5  = md5_file($tmp);
            $name = $md5 . '.' . $type;
            rename($tmp, $out . '/' . $name);
            
            return $name;
        }
    }
}
