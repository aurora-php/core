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
     * Riak database device.
     *
     * @octdoc      c:device/riak
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class riak extends \org\octris\core\db\device
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:riak/__construct
         * @param   string          $host               Host of database server.
         * @param   int             $port               Port of database server.
         */
        public function __construct($host, $port)
        /**/
        {
            parent::__construct();

            $this->addHost(\org\octris\core\db::T_DB_MASTER, array(
                'host'     => $host,
                'port'     => $port
            ));
        }

        /**
         * Add database node connection.
         *
         * @octdoc  m:riak/addNode
         * @param   string          $host               Host of database server.
         * @param   int             $port               Port of database server.
         */
        public function addNode($host, $port)
        /**/
        {
            $this->addHost(\org\octris\core\db::T_DB_SLAVE, array(
                'host'     => $host,
                'port'     => $port
            ));
        }

        /**
         * Create database connection.
         *
         * @octdoc  m:riak/getConnection
         * @param   array                       $options        Host configuration options.
         * @return  \org\octris\core\db\device\onnection_if     Connection to a database.
         */
        protected function createConnection(array $options)
        /**/
        {
            $cn = new \org\octris\core\db\device\riak\connection($this, $options);

            return $cn;
        }
    }
}
