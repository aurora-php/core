<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth {
    /**
     * Interface for building authentication adapters.
     *
     * @octdoc      i:auth/adapter_if
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface adapter_if 
    /**/
    {
        /**
         * Authentication method, needs to be implemented by adapter.
         *
         * @octdoc  m:adapter_if/authenticate
         */
        public function authenticate();
        /**/
    }
}
