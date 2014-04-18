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
     * PDO prepared statement.
     *
     * @octdoc      c:pdo/statement
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class statement
    /**/
    {
        /**
         * Instance of \PDOStatement
         *
         * @octdoc  p:statement/$statement
         * @type    \PDOStatement
         */
        protected $statement;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:statement/__construct
         * @param   \PDOStatement   $statement          The PDO statement object.
         */
        public function __construct(\PDOStatement $statement)
        /**/
        {
            $this->statement = $statement;
        }

        /**
         * Bind parameters to statement.
         *
         * @octdoc  m:statement/bindParam
         */
        public function bindParam(...$params)
        /**/
        {
            $this->statement->bindParam(...$params);
        }

        /**
         * Execute the statement.
         *
         * @octdoc  m:statement/execute
         * @return  \org\octris\core\db\device\pdo\result   Result object.
         */
        public function execute()
        /**/
        {
            $this->statement->execute();

            $result = new \org\octris\core\db\device\pdo\result($this->statement);

            return $result;
        }
    }
}
