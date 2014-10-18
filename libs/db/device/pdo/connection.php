<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\pdo {
    /**
     * PDO connection handler.
     *
     * @octdoc      c:pdo/connection
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class connection implements \org\octris\core\db\device\connection_if
    /**/
    {
        /**
         * Device the connection belongs to.
         *
         * @octdoc  p:connection/$device
         * @type    \org\octris\core\db\device\pdo
         */
        protected $device;
        /**/

        /**
         * Instance of PDO class.
         *
         * @octdoc  p:connection/$pdo
         * @type    \PDO
         */
        protected $pdo;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:connection/__construct
         * @param   \org\octris\core\db\device\pdo  $device             Device the connection belongs to.
         * @param   array                           $options            Connection options.
         */
        public function __construct(\org\octris\core\db\device\pdo $device, array $options)
        /**/
        {
            $this->pdo = new \PDO($options['dsn'], $options['username'], $options['password'], $options['options']);
        }

        /**
         * Release a connection.
         *
         * @octdoc  m:connection/release
         */
        public function release()
        /**/
        {
            //$this->pdo->release();
        }

        /**
         * Check availability of a connection.
         *
         * @octdoc  m:connection/isAlive
         * @return  bool                                        Returns true if connection is alive.
         * @todo    Implement driver specific check.
         */
        public function isAlive()
        /**/
        {
            return true;
        }

        /**
         * Resolve a database reference.
         *
         * @octdoc  m:connection/resolve
         * @param   \org\octris\core\db\type\dbref                          $dbref      Database reference to resolve.
         * @return  bool                                                                Returns false always due to missing implementagtion.
         * @todo    Add implementation.
         */
        public function resolve(\org\octris\core\db\type\dbref $dbref)
        /**/
        {
            return false;
        }

        /**
         * Query the database.
         *
         * @octdoc  m:connection/query
         * @param   string              $statement            SQL statement to perform.
         * @param   mixed               ...$params            Optional additional options.
         * @return  \org\octris\core\db\pdo\result            Query result.
         */
        public function query($statement, ...$params)
        /**/
        {
            if (($res = $this->pdo->query($statement, ...$params)) === false) {
                throw new \Exception($this->errorInfo()[2], $this->errorCode());
            }

            return new \org\octris\core\db\device\pdo\result($res);
        }

        /**
         * Initialize prepared statement.
         *
         * @octdoc  m:connection/prepare
         * @param   string              $statement            SQL statement to prepare.
         * @param   array               $options              Optional additional driver options.
         * @return  \org\octris\core\db\pdo\statement         Instance of a prepared statement.
         */
        public function prepare($statement, array $options = array())
        /**/
        {
            if (($stmt = $this->pdo->prepare($statement, $options)) === false) {
                throw new \Exception('PDO prepare');
            }

            return new \org\octris\core\db\device\pdo\statement($stmt);
        }

        /**
         * Return instance of collection object.
         *
         * @octdoc  m:connection/getCollection
         * @param   string          $name                               Name of collection to return instance of.
         * @return  \org\octris\core\db\device\pdo\collection           Instance of a PDO collection.
         * @todo    Add implementation.
         */
        public function getCollection($name)
        /**/
        {
            // return new \org\octris\core\db\device\pdo\collection(
            //     $this->device,
            //     $name
            // );
        }
    }
}
