<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Device;

/**
 * Interface for database connection.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface IConnection
{
    /**
     * Release connection.
     */
    public function release();

    /**
     * Check availability of a connection.
     */
    public function isAlive();

    /**
     * Resolve a database reference.
     *
     * @param   \Octris\Core\Db\Type\DbRef                          $dbref      Database reference to resolve.
     * @return  \Octris\Core\Db\Device\...\DataObject|bool                      Data object or false if reference could not he resolved.
     */
    public function resolve(\Octris\Core\Db\Type\DbRef $dbref);

    /**
     * Return instance of collection object.
     *
     * @param   string          $name                               Name of collection to return instance of.
     * @return  \Octris\Core\Db\Device\...\Collection           Instance of database collection.
     */
    public function getCollection($name);
    /**/
}
