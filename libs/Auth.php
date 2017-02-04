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
 * Authentication library.
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Auth
{
    /**
     * Authentication status codes.
     */
    const AUTH_PENDING       = 2;
    const AUTH_SUCCESS       = 1;
    const AUTH_FAILURE       = 0;
    const IDENTITY_UNKNOWN   = -1;
    const IDENTITY_AMBIGUOUS = -2;
    const CREDENTIAL_INVALID = -3;

    /**
     * Instance of auth class.
     *
     * @type    \Octris\Core\Auth
     */
    private static $instance = null;

    /**
     * Authentication storage handler.
     *
     * @type    \Octris\Core\Auth\IStorage
     */
    protected $storage;

    /**
     * Constructor.
     */
    protected function __construct()
    {
        $this->storage = new \Octris\Core\Auth\Storage\Transient();
    }

    /*
     * prevent cloning
     */
    private function __clone()
    {
    }

    /**
     * Return instance of auth class, implemented as singleton-pattern.
     *
     * @return  \Octris\Core\Auth                           Authorization class instance.
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Sets the storage handler for authentication information.
     *
     * @param   \Octris\Core\Auth\IStorage    $storage    Instance of storage backend.
     */
    public function setStorage(\Octris\Core\Auth\IStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Test whether there is already an identity authenticated.
     *
     * @return  bool                                            Returns true, if an identity is authenticated.
     */
    public function isAuthenticated()
    {
        if (($return = (!$this->storage->isEmpty()))) {
            $identity = $this->storage->getIdentity();

            $return = (is_object($identity) &&
                        $identity instanceof \Octris\Core\Auth\Identity &&
                        $identity->isValid());
        }

        return $return;
    }

    /**
     * Authenticate againat the specified authentication adapter.
     *
     * @param   \Octris\Core\Auth\IAdapter    $adapter    Instance of adapter to use for authentication.
     * @return  \Octris\Core\Auth\Identity                  The authenticated identity.
     */
    public function authenticate(\Octris\Core\Auth\IAdapter $adapter)
    {
        $identity = $adapter->authenticate();

        $this->storage->setIdentity($identity);

        return $identity;
    }

    /**
     * Returns identity or false, if no identity is available.
     *
     * @return  \Octris\Core\Auth\Identity|bool             Identity or false.
     */
    public function getIdentity()
    {
        return ($this->storage->isEmpty()
                ? false
                : $this->storage->getIdentity());
    }

    /**
     * Remove identity so it is no longer authenticated.
     */
    public function revokeIdentity()
    {
        if (!$this->storage->isEmpty()) {
            $this->storage->unsetIdentity();
        }
    }
}
