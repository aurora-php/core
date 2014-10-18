<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core {
    /**
     * File system class.
     *
     * @octdoc      c:core/fs
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class fs
    /**/
    {
        /**
         * Expand pathname, includes ~ expansion.
         *
         * @octdoc  m:fs/expandPath
         * @param   string                      $path                   Path to expand.
         * @return  string                                              Expanded path.
         */
        public static function expandPath($path)
        /**/
        {
            if (preg_match('/^~(?P<NAME>[_.A-Za-z0-9][-\@_.A-Za-z0-9]*\$?)?((?=\/)|$)/', $path, $match)) {
                $info = ($match['NAME'] == ''
                         ? posix_getpwuid(posix_getuid())
                         : posix_getpwnam($match['NAME']));

                if ($info) {
                    $path = $info['dir'] . '/' . substr($path, strlen($match[0]));
                }
            }
            
            return realpath($path);
        }
    }
}
