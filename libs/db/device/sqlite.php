<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\db\device;

/**
 * SQLite database device. 
 * 
 * @octdoc      c:device/sqlite
 * @copyright   copyright (c) 2012-2013 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 * 
 * @todo        Support encryption Libraries:
 *              * http://sqlcipher.net/
 *              * http://www.hwaci.com/sw/sqlite/see.html
 *              * http://sqlite-crypt.com/index.htm
 */
class sqlite extends \octris\core\db\device
{
    /**
     * SQLite flags of how to open database.
     *
     * @octdoc  d:sqlite/T_READONLY, T_READWRITE, T_CREATE
     * @type    int
     */
    const T_READONLY  = SQLITE3_OPEN_READONLY;
    const T_READWRITE = SQLITE3_OPEN_READWRITE;
    const T_CREATE    = SQLITE3_OPEN_CREATE;
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:sqlite/__construct
     * @param   string          $file               Path to the SQLite database, or :memory: to use in-memory database.
     * @param   int             $flags              Optional flags of how to open SQLite database.
     * @param   string          $key                Optional key when database encryption is used.
     */
    public function __construct($file, $flags = null, $key = null)
    {
        parent::__construct();

        $this->addHost(\octris\core\db::T_DB_MASTER, array(
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
     * @octdoc  m:sqlite/getConnection
     * @param   array                       $options        Host configuration options.
     * @return  \octris\core\db\device\onnection_if     Connection to a database.
     */
    protected function createConnection(array $options)
    {
        $cn = new \octris\core\db\device\sqlite\connection($this, $options);

        return $cn;
    }
}

