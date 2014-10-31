<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Cache\Storage;

/**
 * Redis cache storage.
 *
 * @copyright   copyright (c) 2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Redis extends \Octris\Core\Cache\Storage
{
    /**
     * Instance of redis class.
     *
     * @type    \Redis|null
     */
    protected $redis = null;
    
    /**
     * Constructor.
     *
     * @param   array           $options                Optional cache options.
     */
    public function __construct(array $options = array())
    {
        if (!extension_loaded('redis')) {
            throw new \Exception('Missing redis extension');
        }

        $redis = new Redis();

        if ($redis->connect($options['host'], $options['port'], 1)) {
            $this->redis = $redis;
        }
    }

    /**
     * Make cache iteratable.
     *
     * @return  \redisIterator                            Cache iterator.
     */
    public function getIterator()
    {
        $search = ($this->ns != '' ? '/^' . preg_quote($this->ns, '/') . '/' : null);

        return new \redisIterator('user', $search);
    }

    /**
     * Return metadata from cache for a specified key.
     *
     * @param   string          $key                    The key of the value that should be removed.
     */
    public function getMetaData($key)
    {
    }

    /**
     * Compare and update a value. The value get's only updated, if the current value matches.
     *
     * @param   string          $key                    The key of the value to be updated.
     * @param   int             $v_current              Current stored value.
     * @param   int             $v_new                  New value to store.
     * @return  bool                                    Returns true, if the value was updated.
     */
    public function cas($key, $v_current, $v_new)
    {
    }

    /**
     * Increment a stored value
     *
     * @param   string          $key                    The key of the value to be incremented.
     * @param   int             $step                   The step that the value should be incremented by.
     * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
     * @return  int                                     The updated value.
     */
    public function inc($key, $step, &$success = null)
    {
    }

    /**
     * Decrement a stored value.
     *
     * @param   string          $key                    The key of the value to be decremented.
     * @param   int             $step                   The step that the value should be decremented by.
     * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
     * @return  int                                     The updated value.
     */
    public function dec($key, $step, &$success = null)
    {
    }

    /**
     * Fetch data from cache without populating the cache, if no data is stored for specified id.
     *
     * @param   string          $key                    The key of the value to fetch.
     * @param   bool            $success                Optional parameter that returns true, if the fetch succeeded.
     * @return  mixed                                   The data stored in the cache.
     */
    public function fetch($key, &$success = null)
    {
    }

    /**
     * Load a value from cache or create it from specified callback. In the latter case the created data returned by
     * the callback will be stored in the cache.
     *
     * @param   string          $key                    The key of the value to be loaded.
     * @param   callable        $cb                     Callback to call if the key is not found in the cache.
     * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
     * @return  mixed                                   Stored data.
     */
    public function load($key, callable $cb, $ttl = null)
    {
    }

    /**
     * Store a value to the cache.
     *
     * @param   string          $key                    The key the value should be stored in.
     * @param   mixed           $data                   Arbitrary (almost) data to store.
     * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
     */
    public function save($key, $data, $ttl = null)
    {
    }

    /**
     * Checks if a key exists in the cache.
     *
     * @param   string          $key                    The key to test.
     * @return  bool                                    Returns true if the key exists, otherwise false.
     */
    public function exists($key)
    {
    }

    /**
     * Remove a value from the cache.
     *
     * @param   string          $key                    The key of the value that should be removed.
     */
    public function remove($key)
    {
    }

    /**
     * Clear the entire cache.
     *
     */
    public function clear()
    {
    }
}
