<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core;

/**
 * Cache core class.
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Cache implements \IteratorAggregate
{
    /**
     * Hash algorithm
     *
     * @type    string
     */
    protected $hash_algo = 'adler32';
    
    /**
     * Standard caching backend.
     *
     * @type    \octris\core\cache\IStorage
     */
    protected $backend;
    
    /**
     * Fallback caching backend.
     *
     * @type    \octris\core\cache\IStorage|null
     */
    protected $fallback = null;
    
    /**
     * Logger instance.
     *
     * @type    \Octris\Core\Logger $logger|null
     */
    protected $logger = null;
    
    /**
     * Constructor.
     *
     * @param   \Octris\Core\Cache\Storagen     $storage        Instance of cache storage backend.
     */
    public function __construct(\Octris\Core\Cache\Storage $storage)
    {
        $this->backend = $storage;
    }

    /**
     * Set a fallback cache for example to combine a fast transient and a slower persistent cache,
     * the fallback would define the second, in this example persistent cache, that would be queried,
     * if the first cache does not contain the looked-up data.
     *
     * @param   \Octris\Core\Cache\Storage      $storage        Instance of cache storage backend.
     */
    public function setFallback(\Octris\Core\Cache\Storage $storage)
    {
        $this->fallback = $storage;
    }

    /**
     * Set logger for logging problems and information with cache backends.
     *
     * @param   \Octris\Core\Logger             $logger         Instance of logger class.
     */
    public function setLogger(\Octris\Core\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Hash the input value and create for example as usage for a cache key.
     *
     * @param   mixed                               $data           Data to create hash for.
     * @return  string                                              Cache key.
     */
    public function getCacheKey($data)
    {
        return hash($this->hash_algo, serialize($data));
    }

    /** Proxy **/

    /**
     * Make cache iteratable.
     *
     * @return  \Iterator                               Cache iterator.
     */
    public function getIterator()
    {
        return $this->backend->getIterator();
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
        return $this->backend->cas($key, $v_current, $v_new);
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
        return $this->backend->inc($key, $step, $success);
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
        return $this->backend->dec($key, $step, $success);
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
        return $this->backend->fetch($key, $success);
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
        return $this->backend->load($key, $cb, $ttl);
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
        $this->backend->save($key, $data, $ttl);
    }

    /**
     * Checks if a key exists in the cache.
     *
     * @param   string          $key                    The key to test.
     * @return  bool                                    Returns true if the key exists, otherwise false.
     */
    public function exists($key)
    {
        return $this->backend->exists($key);
    }

    /**
     * Remove a value from the cache.
     *
     * @param   string          $key                    The key of the value that should be removed.
     */
    public function remove($key)
    {
        $this->backend->delete($key);
    }

    /**
     * Clear the entire cache.
     *
     */
    public function clear()
    {
        $this->backend->clear();
    }
}
