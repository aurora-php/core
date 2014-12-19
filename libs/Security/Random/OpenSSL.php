<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Security\Random;

/**
 * Uses openssl module to generate random bytes.
 *
 * @copyright   copyright (c) 2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class OpenSSL implements \Octris\Core\Security\IRandom
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Method returns specified number of random bytes.
     *
     * @param   int                 $bytes                  Number of bytes to generate.
     * @param   bool                $binary                 Optional return binary instead of hex encoded bytes.
     * @return  string|bool                                 Returns number of specified random bytes or false in case of an error.
     */
    public function getRandom($bytes, $binary = false)
    {
        $rnd = openssl_random_pseudo_bytes($bytes);

        return ($binary ? $rnd : bin2hex($rnd));
    }
}
