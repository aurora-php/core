<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Tpl\Postprocess;

/**
 * Try to combine multiple javascript source files into a single file.
 * Compressing is done afterwards using yui compressor.
 *
 * @copyright   copyright (c) 2010-2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class YuiCompressJs extends CombineJs
{
    /**
     * Path to yui compressor.
     *
     * @type    string
     */
    protected $yuic_path;

    /**
     * Optional additional options for yui compressor.
     *
     * @type    array
     */
    protected $options = array('type' => 'js');

    /**
     * Constructor.
     *
     * @param   array       $mappings   Array of path-prefix to real-path mappings.
     * @param   string      $dst        Destination directory for created files.
     * @param   string      $yuic_path  Path to yui compressor.
     * @param   array       $options    Optional additional options for yui compressor.
     */
    public function __construct(array $mappings, $dst, $yuic_path, array $options = array())
    {
        parent::__construct($mappings, $dst);

        $this->yuic_path = $yuic_path;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Process (combine) collected files.
     *
     * @param   array       $files      Files to combine.
     * @return  string                  Destination name.
     */
    public function processFiles(array $files)
    {
        $files = array_map(function ($file) {
            return escapeshellarg($file);
        }, $files);

        $options = array_map(function($k, $v) {
            return escapeshellarg('--' . $k . ' ' . $v);
        }, array_keys($options), array_values($options));

        $tmp = tempnam('/tmp', 'oct');

        $cmd = sprintf(
            'cat %s | java -jar %s/yuicompressor.jar %s -o %s 2>&1',
            implode(' ', $files),
            $this->yuic_path,
            implode(' ', $options),
            $tmp
        );

        $ret = array();
        $ret_val = 0;
        exec($cmd, $ret, $ret_val);

        $md5  = md5_file($tmp);
        $name = $md5 . '.' . $type;
        rename($tmp, $this->dst . '/' . $name);

        return $name;
    }
}
