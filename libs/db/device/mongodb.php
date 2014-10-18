<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device {
    /**
     * MongoDB database device.
     *
     * @octdoc      c:device/mongodb
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class mongodb extends \org\octris\core\db\device
    /**/
    {
        /**
         * Name of database to access.
         *
         * @octdoc  p:mongodb/$database
         * @type    string
         */
        protected $database;
        /**/
        
        /**
         * Username to use for connection.
         *
         * @octdoc  p:mongodb/$username
         * @type    string
         */
        protected $username;
        /**/
        
        /**
         * Password to use for connection.
         *
         * @octdoc  p:mongodb/$password
         * @type    string
         */
        protected $password;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:mongodb/__construct
         * @param   string          $host               Host of database server.
         * @param   int             $port               Port of database server.
         * @param   string          $database           Name of database.
         * @param   string          $username           Optional username to use for connection.
         * @param   string          $password           Optional password to use for connection.
         */
        public function __construct($host, $port, $database, $username = '', $password = '')
        /**/
        {
            parent::__construct();

            $this->addHost(\org\octris\core\db::T_DB_MASTER, array(
                'host'     => $host,
                'port'     => $port,
                'database' => ($this->database = $database),
                'username' => ($this->username = $username),
                'password' => ($this->password = $password),
            ));
        }

        /**
         * Add slave database connection.
         *
         * @octdoc  m:mongodb/addSlave
         * @param   string          $host               Host of database server.
         * @param   int             $port               Port of database server.
         * @param   string          $database           Optional name of database of slave.
         * @param   string          $username           Optional username to use for connection.
         * @param   string          $password           Optional password to use for connection.
         */
        public function addSlave($host, $port, $database = null, $username = null, $password = null)
        /**/
        {
            $this->addHost(\org\octris\core\db::T_DB_SLAVE, array(
                'host'     => $host,
                'port'     => $port,
                'database' => (is_null($database) ? $this->database : $database),
                'username' => (is_null($username) ? $this->username : $username),
                'password' => (is_null($password) ? $this->password : $password) ,
            ));
        }

        /**
         * Create database connection.
         *
         * @octdoc  m:mongodb/getConnection
         * @param   array                       $options        Host configuration options.
         * @return  \org\octris\core\db\device\onnection_if     Connection to a database.
         */
        protected function createConnection(array $options)
        /**/
        {
            $cn = new \org\octris\core\db\device\mongodb\connection($this, $options);

            return $cn;
        }
    }
}
