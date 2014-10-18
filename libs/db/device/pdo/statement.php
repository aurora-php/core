<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\db\device\pdo {
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
        public function bindParam($types, ...$params)
        /**/
        {
            if (strlen($types) != ($cnt = count($types))) {
                throw new \Exception('number of types does not match number of parameters');
            }
            
            $types = str_split($types);
            
            for ($i = 0; $i < $cnt; ++$i) {
                switch ($types[$i]) {
                    case 'i':
                        $type = \PDO::PARAM_INT;
                        $val  = $params[$i];
                        break;
                    case 'd':
                    case 's':
                        $type = \PDO::PARAM_STR;
                        $val  = (string)$params[$i];
                        break;
                    case 'b':
                        $type = \PDO::PARAM_LOB;
                        $val  = $params[$i];
                        break;
                }
                
                $this->statement->bindValue($i + 1, $val, $type);
            }
        }

        /**
         * Execute the statement.
         *
         * @octdoc  m:statement/execute
         * @return  \octris\core\db\device\pdo\result   Result object.
         */
        public function execute()
        /**/
        {
            $this->statement->execute();

            $result = new \octris\core\db\device\pdo\result($this->statement);

            return $result;
        }
    }
}
