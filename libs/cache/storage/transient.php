<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\cache\storage {
    /**
     * Request cache storage. Stores data only within the curren request
     * (transaction / execution).
     *
     * @octdoc      c:storage/transient
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class transient extends \org\octris\core\cache\storage
    /**/
    {
        /**
         * Local data storage.
         *
         * @octdoc  p:transient/$data
         * @type    array
         */
        protected $data = array();
        /**/

        /**
         * Meta data for cache keys.
         * 
         * - ttl   -- time to live
         * - ctime -- time the cache key was created
         * - mtime -- time the cache key was last modified (write)
         * - atime -- time the cache key was last accessed (read)
         *
         * @octdoc  p:transient/$meta
         * @type    array
         */
        protected $meta = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:transient/__construct
         * @param   array           $options                Optional cache options.
         */
        public function __construct(array $options = array())
        /**/
        {
            parent::__construct($options);
        }

        /**
         * Make cache iteratable.
         *
         * @octdoc  m:transient/getIterator
         * @return  \ArrayIterator                          Cache iterator.
         */
        public function getIterator()
        /**/
        {
            return new \ArrayIterator($this->data);
        }

        /**
         * Return metadata from cache for a specified key.
         *
         * @octdoc  m:transient/getMetaData
         * @param   string          $key                    The key of the value that should be removed.
         */
        public function getMetaData($key)
        /**/
        {
        }

        /**
         * Compare and update a value. The value get's only updated, if the current value matches.
         *
         * @octdoc  m:transient/cas
         * @param   string          $key                    The key of the value to be updated.
         * @param   int             $v_current              Current stored value.
         * @param   int             $v_new                  New value to store.
         * @return  bool                                    Returns true, if the value was updated.
         */
        public function cas($key, $v_current, $v_new)
        /**/
        {
            $v_current = (int)$v_current;
            $v_new     = (int)$v_new;

            if (($success = ($this->exists($key) && $this->data[$key] === $v_current))) {
                $this->data[$key] = $v_new;
                $this->meta[$key]['mtime'] = time();
            }

            return $success;
        }

        /**
         * Increment a stored value
         *
         * @octdoc  m:transient/inc
         * @param   string          $key                    The key of the value to be incremented.
         * @param   int             $step                   The step that the value should be incremented by.
         * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
         * @return  int                                     The updated value.
         */
        public function inc($key, $step, &$success = null)
        /**/
        {
            $return = null;

            if (($success = $this->exists($key))) {
                $return = ($this->data[$key] += $step);
                $this->meta[$key]['mtime'] = time();
            }

            return $return;
        }

        /**
         * Decrement a stored value.
         *
         * @octdoc  m:transient/dec
         * @param   string          $key                    The key of the value to be decremented.
         * @param   int             $step                   The step that the value should be decremented by.
         * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
         * @return  int                                     The updated value.
         */
        public function dec($key, $step, &$success = null)
        /**/
        {
            $return = null;

            if (($success = $this->exists($key))) {
                $return = ($this->data[$key] -= $step);
                $this->meta[$key]['mtime'] = time();
            }

            return $return;
        }

        /**
         * Fetch data from cache without populating the cache, if no data is stored for specified id.
         *
         * @octdoc  m:transient/fetch
         * @param   string          $key                    The key of the value to fetch.
         * @param   bool            $success                Optional parameter that returns true, if the fetch succeeded.
         * @return  mixed                                   The data stored in the cache.
         */
        public function fetch($key, &$success = null)
        /**/
        {
            $return = null;

            if (($success = $this->exists($key))) {
                $return = $this->data[$key];
            }

            return $return;
        }

        /**
         * Load a value from cache or create it from specified callback. In the latter case the created data returned by 
         * the callback will be stored in the cache.
         *
         * @octdoc  m:transient/load
         * @param   string          $key                    The key of the value to be loaded.
         * @param   callable        $cb                     Callback to call if the key is not found in the cache.
         * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
         * @return  mixed                                   Stored data.
         */
        public function load($key, callable $cb, $ttl = null)
        /**/
        {
            if (!$this->exists($key)) {
                $this->save($key, $cb(), $ttl);
            }

            return $this->data[$key];
        }

        /**
         * Store a value to the cache.
         *
         * @octdoc  m:transient/save
         * @param   string          $key                    The key the value should be stored in.
         * @param   mixed           $data                   Arbitrary (almost) data to store.
         * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
         */
        public function save($key, $data, $ttl = null)
        /**/
        {
            $t = time();
            $c = (isset($this->meta[$key])
                    ? $this->meta[$key]['ctime']
                    : $t);

            $this->data[$key] = $data;
            $this->meta[$key] = array(
                'ttl'   => ((int)$ttl == 0 ? null : $ttl),
                'ctime' => $c,
                'mtime' => $t,
                'atime' => $t
            );
        }

        /**
         * Checks if a key exists in the cache.
         *
         * @octdoc  m:transient/exists
         * @param   string          $key                    The key to test.
         * @return  bool                                    Returns true if the key exists, otherwise false.
         */
        public function exists($key)
        /**/
        {
            if (($exists = array_key_exists($key, $this->data))) {
                // key exists, test if it's expired
                $exists = !(!is_null($this->meta[$key]['ttl']) && 
                            time() >= $this->meta[$key]['mtime'] + $this->meta[$key]['ttl']);

                if (!$exists) {
                    $this->remove($key);
                }
            }

            return $exists;
        }

        /**
         * Remove a value from the cache.
         *
         * @octdoc  m:transient/remove
         * @param   string          $key                    The key of the value that should be removed.
         */
        public function remove($key)
        /**/
        {
            unset($this->data[$key]);
            unset($this->meta[$key]);
        }

        /**
         * Clear the entire cache.
         *
         * @octdoc  m:transient/clear
         */
        public function clear()
        /**/
        {
            $this->meta = $this->data = array();
        }
    }
}
