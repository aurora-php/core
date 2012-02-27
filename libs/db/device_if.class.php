<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db {
    /**
     * Interface for database devices.
     *
     * @octdoc      i:db/device_if
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface device_if {
		/**
		 * Create database connection.
		 *
		 * @octdoc 	m:device_if/getConnection
		 * @return 	\org\octris\core\db\mongodb\connection 			Connection to a MongoDB database.
		 */
		public getConnection();
		/**/
    }
}
