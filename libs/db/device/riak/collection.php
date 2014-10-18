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
    use \org\octris\core\net\client\http as http;

    /**
     * Riak database collection. Note, that a collection in Riak is called "Bucket" and so this
     * class operates on riak buckets.
     *
     * @octdoc      c:riak/collection
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class collection
    /**/
    {
        /**
         * Device the collection belongs to.
         *
         * @octdoc  p:collection/$device
         * @type    \org\octris\core\db\device\riak
         */
        protected $device;
        /**/

        /**
         * Instance of connection class the collection is access by.
         *
         * @octdoc  p:collection/$connection
         * @type    \org\octris\core\db\device\riak\connection
         */
        protected $connection;
        /**/

        /**
         * Name of collection.
         *
         * @octdoc  p:collection/$name
         * @type    string
         */
        protected $name;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:collection/__construct
         * @param   \org\octris\core\db\device\riak             $device         Device the connection belongs to.
         * @param   \org\octris\core\db\device\riak\connection  $connection     Connection instance.
         * @param   string                                      $name           Name of collection.
         */
        public function __construct(\org\octris\core\db\device\riak $device, \org\octris\core\db\device\riak\connection $connection, $name)
        /**/
        {
            $this->device     = $device;
            $this->connection = $connection;
            $this->name       = $name;
        }

        /**
         * Return name of collection.
         *
         * @octdoc  m:collection/getName
         * @return  string                                          Name of collection.
         */
        public function getName()
        /**/
        {
            return $this->name;
        }

        /**
         * Create an empty object for storing data into specified collection.
         *
         * @octdoc  m:collection/create
         * @param   array                                           $data       Optional data to store in data object.
         * @return  \org\octris\core\db\device\riak\dataobject                  Data object.
         */
        public function create(array $data = array())
        /**/
        {
            $object = new \org\octris\core\db\device\riak\dataobject(
                $this->device,
                $this->getName(),
                $data
            );

            $object->setContentType('application/json');

            return $object;
        }

        /**
         * Fetch the stored item of a specified key.
         *
         * @octdoc  m:collection/fetch
         * @param   string          $key                                Key of item to fetch.
         * @return  \org\octris\core\db\device\riak\dataobject|bool     Either a data object containing the found item or false if no item was found.
         */
        public function fetch($key)
        /**/
        {
            $request = $this->connection->getRequest(
                http::T_GET,
                '/buckets/' . $this->name . '/keys/' . $key
            );
            $result = $request->execute();
            $status = $request->getStatus();
            
            if ($status == 404) {
                // object not found
                $return = false;
            } else {
                $result['_id'] = $key;
                
                // parse link references and populate data with references
                if (($links = $request->getResponseHeader('link')) !== false) {
                    $count = 0;
                    
                    do {
                        $links = preg_replace_callback(
                            '|</buckets/(?P<bucket>[^/]+)/keys/(?P<key>[^/>]+)>; *riaktag="(?P<tag>[^"]+)"|',
                            function($match) use (&$result) {
                                $result[$match['tag']] = new \org\octris\core\db\type\dbref(
                                    $match['bucket'], $match['key']
                                );
                            
                                return '';
                            },
                            $links,
                            -1,
                            $count
                        );
                    } while ($count > 0);
                }
                
                // create dataobject
                $return = new \org\octris\core\db\device\riak\dataobject(
                    $this->device,
                    $this->getName(),
                    $result
                );
            }
            
            return $return;
        }

        /**
         * Query a Riak collection using Riak search interface.
         *
         * @octdoc  m:collection/query
         * @param   array           $query                      Query conditions.
         * @param   int             $offset                     Optional offset to start query result from.
         * @param   int             $limit                      Optional limit of result items.
         * @param   array           $sort                       Optional sorting parameters.
         * @param   array           $fields                     Optional fields to return.
         * @param   array           $hint                       Optional query hint.
         * @return  \org\octris\core\db\device\riak\result      Result object.
         *
         * @ref     http://docs.basho.com/riak/latest/cookbooks/Riak-Search---Indexing-and-Querying-Riak-KV-Data/
         */
        public function query(array $query, $offset = 0, $limit = 20)
        /**/
        {
            if (count($query) == 0) {
                // TODO: list total bucket contents
                return false;
            }
            
            $q = array();
            foreach ($query as $k => $v) {
                $q[] = $k . ':' . $v;
            }
            
            $request = $this->connection->getRequest(
                http::T_GET,
                '/solr/' . $this->name . '/select',
                array(
                    'q'     => implode(' AND ', $q),
                    'start' => $offset,
                    'rows'  => $limit,
                    'wt'    => 'json'
                )
            );

            $result = $request->execute();
            $status = $request->getStatus();

            return new \org\octris\core\db\device\riak\result(
                $this->device,
                $this->getName(),
                $result
            );
        }

        /**
         * Add a link reference header to a request.
         *
         * @octdoc  m:collection/addReferences
         * @param   \org\octris\core\db\riak\request            $request    Request object.
         * @param   \org\octris\core\db\device\riak\dataobject  $object     Data object to collect references from.
         */
        protected function addReferences(\org\octris\core\db\device\riak\request $request, \org\octris\core\db\device\riak\dataobject $object)
        /**/
        {
            $iterator = new \RecursiveIteratorIterator(new \org\octris\core\db\type\recursivedataiterator($object));

            foreach ($iterator as $name => $value) {
                if ($value instanceof \org\octris\core\db\type\dbref) {
                    $request->addHeader(
                        'Link', 
                        sprintf(
                            '</buckets/%s/keys/%s>; riaktag="%s"',
                            urlencode($value->collection),
                            urlencode($value->key),
                            urlencode($name)
                        )
                    );
                }
            }
        }

        /**
         * Insert an object into a database collection.
         *
         * @octdoc  m:collection/insert
         * @param   \org\octris\core\db\device\riak\dataobject  $object     Data to insert into collection.
         * @param   string                                      $key        Optional key to insert.
         * @return  string|bool                                             Returns the inserted key if insert succeeded or false.
         */
        public function insert(\org\octris\core\db\device\riak\dataobject $object, $key = null)
        /**/
        {
            $request = $this->connection->getRequest(
                http::T_POST, 
                '/buckets/' . $this->name . '/keys' . (is_null($key) ? '' : '/' . $key)
            );
            
            $request->addHeader('Content-Type', $object->getContentType());

            $this->addReferences($request, $object);

            $request->execute(json_encode($object));
            
            if (($return = ($request->getStatus() == 201))) {
                $loc = $request->getResponseHeader('location');
                
                $return = substr($loc, strrpos($loc, '/') + 1);
            }

            return $return;
        }

        /**
         * Update data in database collection.
         *
         * @octdoc  m:collection/update
         * @param   \org\octris\core\db\device\riak\dataobject  $object     Data to insert into collection.
         * @param   string                                      $key        Key to update.
         * @return  bool                                                    Returns true if update succeeded otherwise false.
         */
        public function update(\org\octris\core\db\device\riak\dataobject $object, $key)
        /**/
        {
            $request = $this->connection->getRequest(
                http::T_PUT, 
                '/buckets/' . $this->name . '/keys/' . $key
            );
            $request->addHeader('Content-Type', $object->getContentType());

            $this->addReferences($request, $object);
            
            $request->execute(json_encode($object));
            
            return ($request->getStatus() == 200);
        }
    }
}
