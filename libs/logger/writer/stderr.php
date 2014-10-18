<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\logger\writer {
    /**
     * Logger to write messages to stdErr.
     *
     * @octdoc      c:writer/stderr
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class stderr extends \org\octris\core\logger\writer\file
    /**/
    {
        /**
         * Make sure, that parent class will use STDERR for logging.
         *
         * @octdoc  p:stderr/$filename
         * @type    string
         */
        protected $filename = 'php://stderr';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:stderr/__construct
         */
        public function __construct()
        /**/
        {
        }
    }
}
