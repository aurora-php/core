<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\riak {
    /**
     * Riak data object
     *
     * @octdoc      c:riak/dataobject
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class dataobject extends \org\octris\core\db\type\dataobject
    /**/
    {
        /**
         * Headers stored with object.
         *
         * @octdoc  p:dataobject/$headers
         * @type    array
         */
        protected $headers;
        /**/

        /**
         * Content type of stored data.
         *
         * @octdoc  p:dataobject/$content_type
         * @type    string
         */
        protected $content_type = 'application/json';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:dataobject/__construct
         * @param   \org\octris\core\db\device\riak         $device         Device the connection belongs to.
         * @param   string                                  $collection     Name of collection the dataobject belongs to.
         * @param   array                                   $data           Data to initialize dataobject with,
         */
        public function __construct(\org\octris\core\db\device\riak $device, $collection, array $data = array())
        /**/
        {
            parent::__construct($device, $collection, $data);
        }

        /**
         * Set type of content of data stored in the object.
         *
         * @octdoc  m:dataobject/setContentType
         * @param   string                  $content_type               Content type to set.
         */
        public function setContentType($content_type)
        /**/
        {
            $this->content_type = $content_type;
        }

        /**
         * Return content type of data stored in the object.
         *
         * @octdoc  m:dataobject/getContentType
         * @return  string                                              Content type to return.
         */
        public function getContentType()
        /**/
        {
            return $this->content_type;
        }

        /**
         * Get datetime of last modification of the object. The method returns 'null' if the
         * last modified datetime is not set.
         *
         * @octdoc  m:dataobject/getLastModified
         * @return  \DateTime|null                                      Last modified datetime.
         */
        public function getLastModified()
        /**/
        {
            return (isset($this->headers['last-modified'])
                    ? new DateTime($this->headers['last-modified'])
                    : null);
        }

        /** Type casting **/
        
        /**
         * Cast a PHP type to DB internal type.
         *
         * @octdoc  m:dataobject/castPhpToDb
         * @param   mixed               $value              Value to cast.
         * @param   string              $name               Name of the value in the data structure.
         * @return  mixed                                   Casted value.
         */
        protected function castPhpToDb($value, $name)
        /**/
        {
            if (is_object($value)) {
                if ($value instanceof \org\octris\core\type\number) {
                    // number -> float -or- int
                    $return = ($value->isDecimal()
                                ? (float)(string)$value
                                : (int)(string)$value);
                } elseif ($value instanceof \org\octris\core\type\money) {
                    // money -> float
                    $return = (float)(string)$value;
                } elseif ($value instanceof \DateTime) {
                    // datetime -> string
                    $return = $value->format('Y-m-d H:M:S');
                } elseif ($value instanceof \org\octris\core\db\type\dbref) {
                    $return = $value;
                } else {
                    $return = (string)$value;
                }
            } else {
                $return = $value;
            }
            
            return $return;
        }
        
        /**
         * Cast a DB internal type to PHP type.
         *
         * @octdoc  m:dataobject/castDbToPhp
         * @param   mixed               $value              Value to cast.
         * @param   string              $name               Name of the value in the data structure.
         * @return  mixed                                   Casted value.
         */
        protected function castDbToPhp($value, $name)
        /**/
        {
            return $value;
        }

        /**
         * Recursive data iteration and casting for preparing data for export to database. This method 
         * overwrites the default export function to filter all database references from the data.
         *
         * @octdoc  m:dataobject/export
         * @param   array               $data               Data to process.
         */
        protected function export(array &$data)
        /**/
        {
            // filter
            $filter = function($data) use (&$filter) {
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        $data[$key] = $filter($value);
                    } elseif (is_object($value) && $value instanceof \org\octris\core\db\type\dbref) {
                        unset($data[$key]);
                    }
                }

                return $data;
            };

            $data = $filter($data);

            // cast
            parent::export($data);
        }
    }
}
