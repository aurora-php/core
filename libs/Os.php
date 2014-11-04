<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core;

/**
 * Operating system related functionality.
 *
 * @copyright   copyright (c) 2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Os
{
    /**
     * Determine home directory of the current user.
     *
     * @return  string                          Return home directory.
     */
    public static function getHome()
    {
        posix_getpwuid(posix_getuid());

        return $info['dir'];
    }
}
