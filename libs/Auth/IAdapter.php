<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Auth;

/**
 * Interface for building authentication adapters.
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface IAdapter
{
    /**
     * Authentication method, needs to be implemented by adapter.
     */
    public function authenticate();
    /**/
}
