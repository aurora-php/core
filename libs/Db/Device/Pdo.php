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
 * PDO database device.
 *
 * @copyright   copyright (c) 2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Pdo extends \Octris\Core\Db\Device
{
    /**
     * Data Source Name (DSN).
     *
     * @type    string
     */
    protected $dsn;

    /**
     * Username to use for connection.
     *
     * @type    string
     */
    protected $username;

    /**
     * Password to use for connection.
     *
     * @type    string
     */
    protected $password;

    /**
     * Additional options for connection.
     *
     * @type    array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param   string          $dsn                Data Source Name (DSN).
     * @param   string          $username           Optional username to use for connection.
     * @param   string          $password           Optional password to use for connection.
     * @param   array           $options            Optional additional options.
     */
    public function __construct($dsn, $username = null, $password = null, array $options = array())
    {
        parent::__construct();

        $this->addHost(\Octris\Core\Db::DB_MASTER, array(
            'dsn'      => ($this->dsn      = $dsn),
            'username' => ($this->username = $username),
            'password' => ($this->password = $password),
            'options'  => ($this->options  = $options)
        ));
    }

    /**
     * Create DSN from database settings.
     *
     * @param   string                      $device                 Name of device (driver).
     * @param   array                       $settings               Settings for connection to device.
     * @param   array                       $overlay                Optional overlay to overwrite settings with.
     * @return  stdClass                                            Object with the properties DSN, username, password and options.
     */
    public static function createDSN($device, array $settings, array $overlay = array())
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
                $device .= \Octris\Core\Fs::expandPath($settings['path']);
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
     * @param   array                       $options                Host configuration options.
     * @return  \Octris\Core\Db\Device\Pdo\Connection           Connection to a pdo database.
     */
    protected function createConnection(array $options)
    {
        $cn = new \Octris\Core\Db\Device\Pdo\Connection($this, $options);

        return $cn;
    }
}
