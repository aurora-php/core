<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\mongodb {
	/**
	 * MongoDB database device.
	 *
	 * @octdoc		c:mongodb/connection
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
	class connection implements \org\octris\core\db\connection_if, \org\octris\core\db\pool_if
	/**/
	{
		use \org\octris\core\db\pool_tr;

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
         * @param   array                       $options            Connection options.
		 */
		public function __construct(array $options)
		/**/
		{
			$this->mongo = new \Mongo(
				'mongodb://' . $options['host'] . ':' . $options['port'],
				array(
					'username' => $options['username'],
					'password' => $options['password'],
					'db'	   => $options['database']
				)
			);

			$this->db = $this->mongo->selectDB($database);
		}

		/**
		 * Create an empty object for storing data into specified collection.
		 *
		 * @octdoc  m:connection/create
		 * @param   string 			$collection 				Name of collection to create object for.
		 * @return 	\org\octris\core\db\mongodb\dataobject 		Data object.
		 */
		public function create($collection)
		/**/
		{
		    return new \org\octris\core\db\mongodb\dataobject($this->getPool(), $collection);
		}

		/**
		 * Query a MongoDB collection.
		 *
		 * @octdoc  m:connection/query
		 */
		public function query($collection)
		/**/
		{
		    $cl = $this->db->selectCollection($collection);
		    $cl->query();
		}
	}
}
