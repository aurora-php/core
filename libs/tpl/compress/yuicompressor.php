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
     * Compress javascript and css files using the {@link http://developer.yahoo.com/yui/compressor/ yuicompressor}.
     *
     * @octdoc      c:compress/yuicompressor
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class yuicompressor implements \org\octris\core\tpl\compress_if
    /**/
    {
        /**
         * Path
         *
         * @octdoc  p:yuicompressor/$path
         * @type    string
         */
        protected $path;
        /**/
        
        /**
         * Additional options for yuicompressor.
         *
         * @octdoc  p:yuicompressor/$options
         * @type    array
         */
        protected $options = array(
            'js'  => array(),
            'css' => array()
        );
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:yuicompressor/__construct
         * @param   string      $path       Path where "yuicompressor.jar" is located in.
         * @param   array       $options    Optional options for yuicompressor.
         */
        public function __construct($path, array $options = array())
        /**/
        {
            if (!file_exists($path . '/yuicompressor.jar')) {
                throw new \Exception(sprintf('unable to locate "yuicompressor.jar" in "%s"', $path));
            }
            
            $this->path = $path;
            
            // options
            $set_options = function($type, array $defaults = array()) use ($options) {
                $tmp = array_merge(
                    $defaults,
                    (isset($options[$type]) && is_array($options[$type]) ? $options[$type] : array()),
                    array('type' => $type)
                );

                $tmp = array_map(function($k, $v) {
                    return "--$k " . escapeshellarg($v);
                }, array_keys($tmp), array_values($tmp));
                
                $this->options[$type] = $tmp;
            };

            $set_options('js');
            $set_options('css');
        }
        
        /**
         * Execute yuicompressor.
         *
         * @octdoc  m:yuicompressor/exec
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
                'cat %s | java -jar %s/yuicompressor.jar %s -o %s 2>&1',
                implode(' ', $files),
                $this->path,
                implode(' ', $this->options[$type]),
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
