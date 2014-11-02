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
 * SQLite database device.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 *
 * @todo        Support encryption Libraries:
 *              * http://sqlcipher.net/
 *              * http://www.hwaci.com/sw/sqlite/see.html
 *              * http://sqlite-crypt.com/index.htm
 */
class Sqlite extends \Octris\Core\Db\Device
{
    /**
     * SQLite flags of how to open database.
     *
     * @type    int
     */
    const T_READONLY  = SQLITE3_OPEN_READONLY;
    const T_READWRITE = SQLITE3_OPEN_READWRITE;
    const T_CREATE    = SQLITE3_OPEN_CREATE;

    /**
     * Constructor.
     *
     * @param   string          $file               Path to the SQLite database, or :memory: to use in-memory database.
     * @param   int             $flags              Optional flags of how to open SQLite database.
     * @param   string          $key                Optional key when database encryption is used.
     */
    public function __construct($file, $flags = null, $key = null)
    {
        parent::__construct();

        $this->addHost(\Octris\Core\Db::T_DB_MASTER, array(
            'file'  => $file,
            'flags' => (is_null($flags)
                        ? self::T_READWRITE | self::T_CREATE
                        : $flags),
            'key'   => $key
        ));
    }

    /**
     * Create database connection.
     *
     * @param   array                       $options        Host configuration options.
     * @return  \Octris\Core\Db\Device\IConnection     Connection to a database.
     */
    protected function createConnection(array $options)
    {
        $cn = new \Octris\Core\Db\Device\Sqlite\Connection($this, $options);

        return $cn;
    }
}
