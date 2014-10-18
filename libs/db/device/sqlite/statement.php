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
     * SQLite prepared statement.
     *
     * @octdoc      c:sqlite/statement
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class statement
    /**/
    {
        /**
         * Instance of device.
         *
         * @octdoc  p:statement/$device
         * @type    \octris\core\db\device\sqlite
         */
        protected $device;
        /**/

        /**
         * Instance of prepared statement.
         *
         * @octdoc  p:statement/$instance
         * @type    \SQLite3Stmt
         */
        protected $instance;
        /**/

        /**
         * Parameter types.
         *
         * @octdoc  p:statement/$types
         * @type    array
         */
        protected static $types = array(
            'i' => SQLITE3_INTEGER,
            'd' => SQLITE3_FLOAT,
            's' => SQLITE3_TEXT,
            'b' => SQLITE3_BLOB
        );
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:statement/__construct
         * @param   \octris\core\db\device\sqlite   $device         Instance of device.
         * @param   \SQLite3                            $link           Database connection.
         */
        public function __construct(\octris\core\db\device\sqlite $device, \SQLite3Stmt $link)
        /**/
        {
            $this->device   = $device;
            $this->instance = $link;
        }

        /**
         * Returns number of parameters in statement.
         *
         * @octdoc  m:statement/paramCount
         * @return  int                                 Number of parameters.
         */
        public function paramCount()
        /**/
        {
            return $this->instance->paramCount();
        }

        /**
         * Bind parameters to statement.
         *
         * @octdoc  m:statement/bindParam
         * @param   string          $types              String of type identifiers.
         * @param   array           $values             Array of values to bind.
         */
        public function bindParam($types, array $values)
        /**/
        {
            if (preg_match('/[^idsb]/', $types)) {
                throw new \Exception('unknown data type in "' . $types . '"');
            } elseif (strlen($types) != ($cnt1 = count($values))) {
                throw new \Exception('number of specified types and values does not match');
            } elseif ($cnt1 != ($cnt2 = $this->paramCount())) {
                throw new \Exception(
                    sprintf(
                        'number of specified parameters (%d) does not match required parameters (%d)',
                        $cnt1,
                        $cnt2
                    )
                );
            } else {
                for ($i = 0, $len = strlen($types); $i < $len; ++$i) {
                    $this->instance->bindParam(
                        $i + 1,
                        $values[$i],
                        (is_null($values[$i]) ? SQLITE3_NULL : self::$types[$types[$i]])
                    );
                }
            }
        }
        
        /**
         * Execute the prepared statement.
         *
         * @octdoc  m:statement/execute
         * @return  \octris\core\db\device\sqlite\result                Instance of result class.
         */
        public function execute()
        /**/
        {
            $result = $this->instance->execute();
            
            return new \octris\core\db\device\sqlite\result($this->device, $result);
        }
    }
}
