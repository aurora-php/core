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
     * PDO database device.
     *
     * @octdoc      c:device/pdo
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class pdo extends \org\octris\core\db\device
    /**/
    {
        /**
         * Data Source Name (DSN).
         *
         * @octdoc  p:pdo/$dsn
         * @type    string
         */
        protected $dsn;
        /**/
        
        /**
         * Username to use for connection.
         *
         * @octdoc  p:pdo/$username
         * @type    string
         */
        protected $username;
        /**/
        
        /**
         * Password to use for connection.
         *
         * @octdoc  p:pdo/$password
         * @type    string
         */
        protected $password;
        /**/

        /**
         * Additional options for connection.
         *
         * @octdoc  p:pdo/$options
         * @type    array
         */
        protected $options;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:pdo/__construct
         * @param   string          $dsn                Data Source Name (DSN).
         * @param   string          $username           Optional username to use for connection.
         * @param   string          $password           Optional password to use for connection.
         * @param   array           $options            Optional additional options.
         */
        public function __construct($dsn, $username = null, $password = null, array $options = array())
        /**/
        {
            parent::__construct();

            $this->addHost(\org\octris\core\db::T_DB_MASTER, array(
                'dsn'      => ($this->dsn      = $dsn),
                'username' => ($this->username = $username),
                'password' => ($this->password = $password),
                'options'  => ($this->options  = $options)
            ));
        }

        /**
         * Create DSN from database settings.
         *
         * @octdoc  m:pdo/createDSN
         * @param   string                      $device                 Name of device (driver).
         * @param   array                       $settings               Settings for connection to device.
         * @param   array                       $overlay                Optional overlay to overwrite settings with.
         * @return  stdClass                                            Object with the properties DSN, username, password and options.
         */
        public static function createDSN($device, array $settings, array $overlay = array())
        /**/
        {
            $config   = array();
            $settings = array_merge($settings, $overlay);
            $device  .= ':';
            
            switch ($device) {
                case 'cubrid:':
                case 'pqsql:':
                    list($config['host'], $config['port']) = explode(':', $settings['host'] . ':33000');
                    
                    $config['dbname'] = $settings['database'];
                    break;
                case 'dblib:':
                case 'mssql:':
                case 'sybase:':
                    $config['host']    = $settings['host'];
                    $config['dbname']  = $settings['database'];

                    if (isset($settings['charset'])) {
                        $config['charset'] = $settings['charset'];
                    }
                    if (isset($settings['appname'])) {
                        $config['appname'] = $settings['appname'];
                    }
                    break;
                case 'mysql:':
                    if (isset($settings['unix_socket'])) {
                        $config['unix_socket'] = $settings['unix_socket'];
                    } else {
                        list($config['host'], $config['port']) = explode(':', $settings['host'] . ':3306');
                    }

                    $config['dbname'] = $settings['database'];
                    
                    if (isset($settings['charset'])) {
                        $config['charset'] = $settings['charset'];
                    }
                    break;
                case 'oci:':
                    if (isset($settings['host'])) {
                        $device .= '//' . $settings['host'] . '/';
                    }
                    
                    $device .= $settings['database'];

                    if (isset($settings['charset'])) {
                        $device .= ';charset=' . $settings['charset'];
                    }
                    break;
                case 'sqlite:':
                    $device .= \org\octris\core\fs::expandPath($settings['path']);
                    break;
            }
            
            $return = new \stdClass;
            $return->dsn      = $device . http_build_query($config, null, ';');
            $return->username = (isset($settings['username']) ? $settings['username'] : '');
            $return->password = (isset($settings['password']) ? $settings['password'] : '');
            $return->options  = (isset($settings['options']) ? $settings['options'] : array());
            
            return $return;
        }

        /**
         * Create database connection.
         *
         * @octdoc  m:pdo/createConnection
         * @param   array                       $options                Host configuration options.
         * @return  \org\octris\core\db\device\pdo\connection           Connection to a pdo database.
         */
        protected function createConnection(array $options)
        /**/
        {
            $cn = new \org\octris\core\db\device\pdo\connection($this, $options);

            return $cn;
        }
    }
}
