<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\cache\storage {
    /**
     * Filesystem cache storage.
     *
     * @octdoc      c:storage/file
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class file extends \octris\core\cache\storage
    {
        /**
         * Namespace separator.
         *
         * @octdoc  p:file/$ns_separator
         * @type    string
         */
        protected $ns_separator = '/';
        /**/

        /**
         * Cache path.
         *
         * @octdoc  p:file/$path
         * @type    string
         */
        protected $path;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:file/__construct
         * @param   array           $options                Optional cache options.
         */
        public function __construct(array $options = array())
        {
            parent::__construct($options);

            $path = (isset($options['path'])
                        ? rtrim($options['path'], '/')
                        : '/tmp');

            if ($this->ns != '') $path .= '/' . $this->ns;

            $this->path = $path;

            if (!is_dir($this->path) && !is_writable($this->path)) {
                if (!@mkdir($this->path, 0777, true)) {
                    throw new \Exception('Unable to create cache directory "' . $this->path . '".');
                }
            }
        }

        /**
         * Serialize data and write it to a cache file. To make the writing of a cache file an atomic operation, 
         * a temporary file is used to save the data and the atomic rename function is than used to move the temp-
         * file to it's final destination.
         *
         * @octdoc  m:file/putContent
         * @param   string          $key                    Key of data to store in cache.
         * @param   mixed           $data                   Data to store in cache.
         * @param   int             $ttl                    Optional ttl of cache item.
         */
        protected function putContent($key, $data, $ttl)
        {
            $file = $this->path . '/' . $key . '.ser';
            $tmp  = tempnam('/tmp', 'cf');

            file_put_contents($tmp, serialize(array('meta' => $this->createMetaData($ttl), 'data' => $data)));

            rename($tmp, $file);
        }

        /**
         * Get data from file.
         *
         * @octdoc  m:file/getContent
         * @param   string          $key                    Key of data stored in cache.
         * @return  array|bool                              Returns false if content could not be loaded or an array with first item is the meta data and second item is the cached data.
         */
        public function getContent($key)
        {
            $file = $this->path . '/' . $key . '.ser';

            if (($return = (is_file($file) && is_readable($file)))) {
                $return = unserialize(file_get_contents($file));
            }

            return $return;
        }

        /**
         * Execute stat for specified cache key.
         *
         * @octdoc  m:file/getStat
         * @param   string          $key                    Key of data stored in cache.
         * @return  array|bool                              Returns stat data or false, if file does not exist.
         */
        public function getStat($key)
        {
            clearstatcache();

            try {
                $stat = stat($this->path . '/' . $key . '.ser');
            } catch(\Exception $e) {
                $stat = false;
            }

            return $stat;
        }

        /**
         * Test if cache key is already expired.
         *
         * @octdoc  m:file/isExpired
         * @param   string          $key                    Key of data stored in cache.
         * @param   int             $ttl                    Optional ttl of cache item.
         * @return  bool                                    Returns true if cache item is expired.
         */
        public function isExpired($key, $ttl = null)
        {
            $ttl = (is_null($ttl) ? $this->ttl : $ttl);

            return (!(($stat = $this->getStat($key)) && ($ttl === -1 || $stat['mtime'] + $ttl > time())));
        }

        /**
         * Return metadata from cache for a specified key.
         *
         * @octdoc  m:storage/getMetaData
         * @param   string          $key                    The key of the value that should be removed.
         */
        public function getMetaData($key)
        {
        }

        /**
         * Make cache iteratable.
         *
         * @octdoc  m:file/getIterator
         */
        public function getIterator()
        {
            throw new \Exception('The method "' . __METHOD__ . '" is not currently implemented in this backend!');
        }

        /**
         * Compare and update a value. The value get's only updated, if the current value matches.
         *
         * @octdoc  m:file/cas
         * @param   string          $key                    The key of the value to be updated.
         * @param   int             $v_current              Current stored value.
         * @param   int             $v_new                  New value to store.
         * @return  bool                                    Returns true, if the value was updated.
         */
        public function cas($key, $v_current, $v_new)
        {
            throw new \Exception('The method "' . __METHOD__ . '" is not currently implemented in this backend!');
        }

        /**
         * Increment a stored value
         *
         * @octdoc  m:file/inc
         * @param   string          $key                    The key of the value to be incremented.
         * @param   int             $step                   The step that the value should be incremented by.
         * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
         * @return  int                                     The updated value.
         */
        public function inc($key, $step, &$success = null)
        {
            throw new \Exception('The method "' . __METHOD__ . '" is not currently implemented in this backend!');
        }

        /**
         * Decrement a stored value.
         *
         * @octdoc  m:file/dec
         * @param   string          $key                    The key of the value to be decremented.
         * @param   int             $step                   The step that the value should be decremented by.
         * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
         * @return  int                                     The updated value.
         */
        public function dec($key, $step, &$success = null)
        {
            throw new \Exception('The method "' . __METHOD__ . '" is not currently implemented in this backend!');
        }

        /**
         * Fetch data from cache without populating the cache, if no data is stored for specified id.
         *
         * @octdoc  m:file/fetch
         * @param   string          $key                    The key of the value to fetch.
         * @param   bool            $success                Optional parameter that returns true, if the fetch succeeded.
         * @return  mixed                                   The data stored in the cache.
         */
        public function fetch($key, &$success = null)
        {
            if (($return = $success = !$this->isExpired($key))) {
                if (($return = $this->getContent($key))) {
                    $return = $return['data'];
                }
            }

            return $return;
        }

        /**
         * Load a value from cache or create it from specified callback. In the latter case the created data returned by 
         * the callback will be stored in the cache.
         *
         * @octdoc  m:file/load
         * @param   string          $key                    The key of the value to be loaded.
         * @param   callable        $cb                     Callback to call if the key is not found in the cache.
         * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
         * @return  mixed                                   Stored data.
         */
        public function load($key, callable $cb, $ttl = null)
        {
            if (($return = !$this->isExpired($key, $ttl))) {
                list(, $return) = $this->getContent($key);
            } else {
                $return = $cb();

                $this->putContent($key, $return, $ttl);
            }

            return $return;
        }

        /**
         * Store a value to the cache.
         *
         * @octdoc  m:file/save
         * @param   string          $key                    The key the value should be stored in.
         * @param   mixed           $data                   Arbitrary (almost) data to store.
         * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
         */
        public function save($key, $data, $ttl = null)
        {
            $this->putContent($key, $data, $ttl);
        }

        /**
         * Checks if a key exists in the cache.
         *
         * @octdoc  m:file/exists
         * @param   string          $key                    The key to test.
         * @return  bool                                    Returns true if the key exists, otherwise false.
         */
        public function exists($key)
        {
            return (!$this->isExpired($key));
        }

        /**
         * Remove a value from the cache.
         *
         * @octdoc  m:file/remove
         * @param   string          $key                    The key of the value that should be removed.
         */
        public function remove($key)
        {
            // TODO
        }

        /**
         * Clear the entire cache.
         *
         * @octdoc  m:file/clear
         */
        public function clear()
        {
            // TODO
        }
    }
}
