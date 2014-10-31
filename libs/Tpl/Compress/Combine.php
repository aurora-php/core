<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Tpl\Compress;

/**
 * Only combine files without compression.
 *
 * @copyright   copyright (c) 2013 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Combine implements \Octris\Core\Tpl\ICompress
{
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * Execute combine.
     *
     * @param   array       $files      Files to compress.
     * @param   string      $out        Name of path to store file in.
     * @param   string      $inp        Name of base-path to lookup source file in.
     * @param   string      $type       Type of files to compress.
     * @return  string                  Name of created file.
     */
    public function exec($files, $out, $inp, $type)
    {
        array_walk($files, function (&$file) use ($inp) {
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
