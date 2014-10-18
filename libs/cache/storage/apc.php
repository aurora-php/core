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
     * APC cache storage.
     *
     * @octdoc      c:storage/apc
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class apc extends \org\octris\core\cache\storage
    /**/
    {
        /**
         * Required minimal APC version
         * 
         * @octdoc  d:apc/T_APC_VERSION
         */
        const T_APC_VERSION = '3.1.6';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:apc/__construct
         * @param   array           $options                Optional cache options.
         */
        public function __construct(array $options = array())
        /**/
        {
            if (version_compare(self::T_APC_VERSION, phpversion('apc')) > 0) {
                throw new \Exception('Missing ext/apc >= ' . self::T_APC_VERSION);
            }

            if (!(ini_get('apc.enabled') && (PHP_SAPI != 'cli' || ini_get('apc.enable_cli')))) {
                throw new \Exception('ext/apc is disabled');
            }

            parent::__construct($options);
        }

        /**
         * Make cache iteratable.
         *
         * @octdoc  m:apc/getIterator
         * @return  \APCIterator                            Cache iterator.
         */
        public function getIterator()
        /**/
        {
            $search = ($this->ns != '' ? '/^' . preg_quote($this->ns, '/') . '/' : null);

            return new \APCIterator('user', $search);
        }

        /**
         * Return metadata from cache for a specified key.
         *
         * @octdoc  m:apc/getMetaData
         * @param   string          $key                    The key of the value that should be removed.
         */
        public function getMetaData($key)
        /**/
        {
        }

        /**
         * Compare and update a value. The value get's only updated, if the current value matches.
         *
         * @octdoc  m:apc/cas
         * @param   string          $key                    The key of the value to be updated.
         * @param   int             $v_current              Current stored value.
         * @param   int             $v_new                  New value to store.
         * @return  bool                                    Returns true, if the value was updated.
         */
        public function cas($key, $v_current, $v_new)
        /**/
        {
            return apc_cas($this->ns . $key, $v_current, $v_new);
        }

        /**
         * Increment a stored value
         *
         * @octdoc  m:apc/inc
         * @param   string          $key                    The key of the value to be incremented.
         * @param   int             $step                   The step that the value should be incremented by.
         * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
         * @return  int                                     The updated value.
         */
        public function inc($key, $step, &$success = null)
        /**/
        {
            return apc_inc($this->ns . $key, $step, $success);
        }

        /**
         * Decrement a stored value.
         *
         * @octdoc  m:apc/dec
         * @param   string          $key                    The key of the value to be decremented.
         * @param   int             $step                   The step that the value should be decremented by.
         * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
         * @return  int                                     The updated value.
         */
        public function dec($key, $step, &$success = null)
        /**/
        {
            return apc_dec($this->ns . $key, $step, $success);
        }

        /**
         * Fetch data from cache without populating the cache, if no data is stored for specified id.
         *
         * @octdoc  m:apc/fetch
         * @param   string          $key                    The key of the value to fetch.
         * @param   bool            $success                Optional parameter that returns true, if the fetch succeeded.
         * @return  mixed                                   The data stored in the cache.
         */
        public function fetch($key, &$success = null)
        /**/
        {
            return apc_fetch($this->ns . $key, $success);
        }

        /**
         * Load a value from cache or create it from specified callback. In the latter case the created data returned by 
         * the callback will be stored in the cache.
         *
         * @octdoc  m:apc/load
         * @param   string          $key                    The key of the value to be loaded.
         * @param   callable        $cb                     Callback to call if the key is not found in the cache.
         * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
         * @return  mixed                                   Stored data.
         */
        public function load($key, callable $cb, $ttl = null)
        /**/
        {
            if (apc_exists($this->ns . $key)) {
                $data = apc_fetch($this->ns . $key);
            } else {
                $data = $cb();

                apc_store($this->ns . $key, $data, (is_null($ttl) ? $this->ttl : $ttl));
            }

            return $data;
        }

        /**
         * Store a value to the cache.
         *
         * @octdoc  m:apc/save
         * @param   string          $key                    The key the value should be stored in.
         * @param   mixed           $data                   Arbitrary (almost) data to store.
         * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
         */
        public function save($key, $data, $ttl = null)
        /**/
        {
            apc_store($this->ns . $key, $data, (is_null($ttl) ? $this->ttl : $ttl));
        }

        /**
         * Checks if a key exists in the cache.
         *
         * @octdoc  m:apc/exists
         * @param   string          $key                    The key to test.
         * @return  bool                                    Returns true if the key exists, otherwise false.
         */
        public function exists($key)
        /**/
        {
            return apc_exists($this->ns . $key);
        }

        /**
         * Remove a value from the cache.
         *
         * @octdoc  m:apc/remove
         * @param   string          $key                    The key of the value that should be removed.
         */
        public function remove($key)
        /**/
        {
            apc_delete($this->ns . $key);
        }

        /**
         * Clear the entire cache.
         *
         * @octdoc  m:apc/clear
         */
        public function clear()
        /**/
        {
            if ($this->ns) {
                apc_delete(new APCIterator('user', '/^' . preg_quote($this->ns, '/') . '/'));
            } else {
                apc_clear_cache('user');
            }
        }
    }
}
