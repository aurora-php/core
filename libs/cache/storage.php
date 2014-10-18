<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\cache {
    /**
     * Cache storage base class.
     *
     * @octdoc      c:cache/storage
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class storage implements \IteratorAggregate
    /**/
    {
        /**
         * Storage namespace.
         *
         * @octdoc  p:storage/$ns
         * @type    string
         */
        protected $ns = '';
        /**/
        
        /**
         * Namespace separator.
         *
         * @octdoc  p:storage/$ns_separator
         * @type    string
         */
        protected $ns_separator = ':';
        /**/

        /**
         * Time to live in seconds.
         *
         * @octdoc  p:storage/$ttl
         * @type    int
         */
        protected $ttl = 0;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:storage/__construct
         * @param   array           $options                Optional cache options.
         */
        public function __construct(array $options = array())
        /**/
        {
            if (isset($options['ns_separator'])) {
                $this->ns_separator = $options['ns_separator'];
            }
            if (isset($options['ns'])) {
                if (preg_match('/[^0-9_a-z' . preg_quote($this->ns_separator, '/') . ']/i', $options['ns'])) {
                    throw new \Exception(sprintf(
                        'The namespace can only contain the characters "0-9", "a-z", "A-Z", "_" and "%s"',
                        $this->ns_separator
                    ));
                } else {
                    $this->ns = $options['ns'] . $this->ns_separator;
                }
            }
            if (isset($options['ttl'])) {
                $this->ttl = $options['ttl'];
            }
        }

        /**
         * Create set of metadata intended to be stored with data to cache.
         *
         * @octdoc  m:storage/createMetaData
         * @param   int             $ttl                    Optional ttl.
         * @return  array                                   Array containing created meta data.
         */
        public function createMetaData($ttl = null)
        /**/
        {
            $mtime = time();
            $ttl   = (is_null($ttl) ? $this->ttl : $ttl);

            return array(
                'mtime'     => $mtime,
                'expires'   => $mtime + $ttl,
                'ttl'       => $ttl
            );
        }

        /** methods that need to be implemented by child class **/

        /**
         * Return metadata from cache for a specified key.
         *
         * @octdoc  m:storage/getMetaData
         * @param   string          $key                    The key of the value that should be removed.
         */
        abstract public function getMetaData($key);
        /**/

        /**
         * Compare and update a value. The value get's only updated, if the current value matches. The name of the
         * method CAS means: 'Compare And Swap'.
         *
         * @octdoc  m:storage/cas
         * @param   string          $key                    The key of the value to be updated.
         * @param   int             $v_current              Current stored value.
         * @param   int             $v_new                  New value to store.
         * @return  bool                                    Returns true, if the value was updated.
         */
        abstract public function cas($key, $v_current, $v_new);
        /**/

        /**
         * Increment a stored value
         *
         * @octdoc  m:storage/inc
         * @param   string          $key                    The key of the value to be incremented.
         * @param   int             $step                   The step that the value should be incremented by.
         * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
         * @return  int                                     The updated value.
         */
        abstract public function inc($key, $step, &$success = null);
        /**/

        /**
         * Decrement a stored value.
         *
         * @octdoc  m:storage/dec
         * @param   string          $key                    The key of the value to be decremented.
         * @param   int             $step                   The step that the value should be decremented by.
         * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
         * @return  int                                     The updated value.
         */
        abstract public function dec($key, $step, &$success = null);
        /**/

        /**
         * Fetch data from cache without populating the cache, if no data is stored for specified id.
         *
         * @octdoc  m:storage/fetch
         * @param   string          $key                    The key of the value to fetch.
         * @param   bool            $success                Optional parameter that returns true, if the fetch succeeded.
         * @return  mixed                                   The data stored in the cache.
         */
        abstract public function fetch($key, &$success = null);
        /**/

        /**
         * Load a value from cache or create it from specified callback. In the latter case the created data returned by 
         * the callback will be stored in the cache.
         *
         * @octdoc  m:storage/load
         * @param   string          $key                    The key of the value to be loaded.
         * @param   callable        $cb                     Callback to call if the key is not found in the cache.
         * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
         * @return  mixed                                   Stored data.
         */
        abstract public function load($key, callable $cb, $ttl = null);
        /**/

        /**
         * Store a value to the cache.
         *
         * @octdoc  m:storage/save
         * @param   string          $key                    The key the value should be stored in.
         * @param   mixed           $data                   Arbitrary (almost) data to store.
         * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
         */
        abstract public function save($key, $data, $ttl = null);
        /**/

        /**
         * Checks if a key exists in the cache.
         *
         * @octdoc  m:storage/exists
         * @param   string          $key                    The key to test.
         * @return  bool                                    Returns true if the key exists, otherwise false.
         */
        abstract public function exists($key);
        /**/

        /**
         * Remove a value from the cache.
         *
         * @octdoc  m:storage/remove
         * @param   string          $key                    The key of the value that should be removed.
         */
        abstract public function remove($key);
        /**/

        /**
         * Clear the entire cache.
         *
         * @octdoc  m:storage/clear
         */
        abstract public function clear();
        /**/
    }
}
