<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\mongodb {
    /**
     * MongoDB database connection.
     *
     * @octdoc      c:mongodb/connection
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class connection implements \org\octris\core\db\device\connection_if
    /**/
    {
        /**
         * Device the connection belongs to.
         *
         * @octdoc  p:connection/$device
         * @var     \org\octris\core\db\device\mongodb
         */
        protected $device;
        /**/

        /**
         * Instance of mongo class.
         *
         * @octdoc  p:connection/$mongo
         * @var     \Mongo
         */
        protected $mongo;
        /**/

        /**
         * Connection to a database.
         *
         * @octdoc  p:connection/$db
         * @var     \MongoDB
         */
        protected $db;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:connection/__construct
         * @param   \org\octris\core\db\device\mongodb  $device             Device the connection belongs to.
         * @param   array                               $options            Connection options.
         */
        public function __construct(\org\octris\core\db\device\mongodb $device, array $options)
        /**/
        {
            $this->device = $device;
            $this->mongo  = new \Mongo(
                'mongodb://' . $options['host'] . ':' . $options['port'],
                array(
                    // 'username' => $options['username'],
                    // 'password' => $options['password'],
                    'db'       => $options['database']
                )
            );

            $this->db = $this->mongo->selectDB($options['database']);
        }

        /**
         * Release connection.
         *
         * @octdoc  m:connection/release
         */
        public function release()
        /**/
        {
            $this->device->release($this);
        }

        /**
         * Resolve a database reference.
         *
         * @octdoc  m:connection/resolve
         * @param   array           $ref                                Database reference to resolve.
         * @param   \org\octris\core\db\device\mongodb\dataobject       Data object.
         */
        public function resolve(array $ref)
        /**/
        {
            $return = false;

            if (!\MongoDBRef::isRef($ref)) {
                throw new \Exception('no database reference provided');
            } else {
                $cl = $this->db->selectCollection($collection);

                $data = $cl->getDBRef($ref);

                $return = new \org\octris\core\db\device\mongodb\dataobject($this->device, $collection, $data);
            }

            return $return;
        }

        /**
         * Execute a database command.
         *
         * @octdoc  m:connection/command
         * @param   array           $command                    Command to execute in database.
         * @param   array           $options                    Optional options for command.
         * @return  mixed                                       Return value of executed command.
         */
        public function command(array $command, array $options = array())
        /**/
        {
            return $this->db->command($command, $options);
        }

        /**
         * Return instance of collection object.
         *
         * @octdoc  m:connection/getCollection
         * @param   string          $name                               Name of collection to return instance of.
         * @return  \org\octris\core\db\device\mongodb\collection
         */
        public function getCollection($name)
        /**/
        {
            return new \org\octris\core\db\device\mongodb\collection(
                $this->device, 
                $this->db->selectCollection($name)
            );
        }
    }
}
