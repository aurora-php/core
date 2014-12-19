<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Security;

/**
 * Interface for random byte generators.
 *
 * @copyright   copyright (c) 2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface IRandom
{
    /**
     * Method returns specified number of random bytes.
     *
     * @param   int                 $bytes                  Number of bytes to generate.
     * @param   bool                $binary                 Optional return binary instead of hex encoded bytes.
     * @return  string|bool                                 Returns number of specified random bytes or false in case of an error.
     */
    public function getRandom($bytes, $binary = false);
    /**/
}
