<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\db\device\sqlite {
    /**
     * SQLite connection handler.
     *
     * @octdoc      c:sqlite/connection
     * @copyright   copyright (c) 2012-2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class connection extends \SQLite3 implements \octris\core\db\device\connection_if
    {
        /**
         * Device the connection belongs to.
         *
         * @octdoc  p:connection/$device
         * @type    \octris\core\db\device\sqlite
         */
        protected $device;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:connection/__construct
         * @param   \octris\core\db\device\sqlite   $device             Device the connection belongs to.
         * @param   array                               $options            Connection options.
         */
        public function __construct(\octris\core\db\device\sqlite $device, array $options)
        {
            $this->device = $device;
            
            parent::__construct($options['file'], $options['flags'], $options['key']);
        }

        /**
         * Release connection.
         *
         * @octdoc  m:connection/release
         */
        public function release()
        {
            $this->device->release($this);
        }

        /**
         * Check connection.
         *
         * @octdoc  m:connection/isAlive
         * @return  bool                                            Returns true if the connection is alive.
         */
        public function isAlive()
        {
            return true;
        }

        /**
         * Resolve a database reference.
         *
         * @octdoc  m:connection_if/resolve
         * @todo    Implementation.
         * @param   \octris\core\db\type\dbref                          $dbref      Database reference to resolve.
         * @return  \octris\core\db\device\sqlite\dataobject|bool                   Data object or false if reference could not he resolved.
         */
        public function resolve(\octris\core\db\type\dbref $dbref)
        {
            return false;
        }

        /**
         * Return list of collections.
         *
         * @octdoc  m:connection/getCollections
         * @return  array|bool                                      Array of names of collections or false in case
         *                                                          of an error.
         */
        public function getCollections()
        {
            $sql = 'SELECT  * 
                    FROM    sqlite_master 
                    WHERE   type="table"';
            
            $result = $this->query($sql);
            $return = array();
            
            foreach ($result as $row) {
                $return[] = $row['name'];
            }
            
            return $return;
        }

        /**
         * Return instance of collection object.
         *
         * @octdoc  m:connection/getCollection
         * @param   string          $name                               Name of collection to return instance of.
         * @return  \octris\core\db\device\sqlite\collection        Instance of sqlte collection.
         */
        public function getCollection($name)
        {
            return new \octris\core\db\device\sqlite\collection(
                $this->device,
                $this,
                $name
            );
        }

        /**
         * Initialize prepared statement.
         *
         * @octdoc  m:connection/prepare
         * @param   string                      $sql                SQL statement to use as prepared statement.
         * @return  \octris\core\db\sqlite\statement            Instance of prepared statement.
         */
        public function prepare($sql)
        {
            $stmt = parent::prepare($sql);
            
            return new \octris\core\db\device\sqlite\statement($this->device, $stmt);
        }
        
        /**
         * Execute a SQL query.
         *
         * @octdoc  m:connection/query
         * @param   string                      $sql                SQL statement to execute.
         * @return  \octris\core\db\sqlite\result               Instance of result class.
         */
        public function query($sql)
        {
            $result = parent::query($sql);
            
            return new \octris\core\db\device\sqlite\result($this->device, $result);
        }
    }
}
