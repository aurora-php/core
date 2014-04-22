<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\security\random {
    /**
     * Uses mcrypt module to generate random bytes.
     *
     * @octdoc      c:random/mcrypt
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class mcrypt implements \org\octris\core\security\random_if
    /**/
    {
        /**
         * Source of random bytes.
         *
         * @octdoc  p:mcrypt/$source
         * @type    int
         */
        protected $source;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:mcrypt/__construct
         * @param   int                 $source                 Source for random bytes.
         */
        public function __construct($source = MCRYPT_DEV_URANDOM)
        /**/
        {
            $this->source = $source;
        }
        
        /**
         * Method returns specified number of random bytes.
         *
         * @octdoc  m:mcrypt/getRandom
         * @param   int                 $bytes                  Number of bytes to generate.
         * @param   bool                $binary                 Optional return binary instead of hex encoded bytes.
         * @return  string|bool                                 Returns number of specified random bytes or false in case of an error.
         */
        public function getRandom($bytes, $binary = false)
        /**/
        {
            $rnd = mcrypt_create_iv($bytes, $this->source);
            
            return ($binary ? $rnd : bin2hex($rnd));
        }
    }
}
