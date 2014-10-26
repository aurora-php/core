<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core;

/**
 * Core database class.
 *
 * @octdoc      c:core/db
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class db
{
    /**
     * Types of database connections.
     *
     * @octdoc  d:db/T_DB_MASTER, T_DB_SLAVE
     */
    const T_DB_MASTER = 'master';
    const T_DB_SLAVE  = 'slave';
    /**/
}
