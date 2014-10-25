<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\db\device {
    /**
     * Interface for database connection.
     *
     * @octdoc      i:device/connection_if
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface connection_if 
    {
        /**
         * Release connection.
         *
         * @octdoc  m:connection_if/release
         */
        public function release();
        /**/
        
        /**
         * Check availability of a connection.
         *
         * @octdoc  m:connection_if/isAlive
         */
        public function isAlive();
        /**/

        /**
         * Resolve a database reference.
         *
         * @octdoc  m:connection_if/resolve
         * @param   \octris\core\db\type\dbref                          $dbref      Database reference to resolve.
         * @return  \octris\core\db\device\...\dataobject|bool                      Data object or false if reference could not he resolved.
         */
        public function resolve(\octris\core\db\type\dbref $dbref);
        /**/

        /**
         * Return instance of collection object.
         *
         * @octdoc  m:connection/getCollection
         * @param   string          $name                               Name of collection to return instance of.
         * @return  \octris\core\db\device\...\collection           Instance of database collection.
         */
        public function getCollection($name);
        /**/
    }
}
