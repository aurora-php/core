<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Auth;

/**
 * Class for storing authenticated identity.
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Identity
{
    /**
     * Authentication status code.
     *
     * @type    int
     */
    protected $code;

    /**
     * Properties stored in the identity.
     *
     * @type    array
     */
    protected $identity = array();

    /**
     * Roles assigned to the identity.
     *
     * @type    array
     */
    protected $roles = array('guest');

    /**
     * Construct.
     *
     * @param   int             $code                   Status code.
     * @param   array           $identity               Settings, that are stored in the identity.
     */
    public function __construct($code, array $identity)
    {
        $this->code     = $code;
        $this->identity = $identity;
    }

    /**
     * Method is called, when identity object get's serialized, for example when it's saved in the
     * storage.
     *
     * @return  array                                   Field names to serialize.
     */
    public function __sleep()
    {
        return array('code', 'identity', 'roles');
    }

    /**
     * Returns true, if identity is valid.
     *
     * @param   bool                                    Identity validation status.
     */
    public function isValid()
    {
        return ($this->code === \Octris\Core\Auth::AUTH_SUCCESS);
    }

    /**
     * Return status code of identity authentication.
     *
     * @param   int                                     Status code.
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns the stored identity data.
     *
     * @param   array                                   Identity data.
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Set roles for identity.
     *
     * @param   array           $roles                  Roles to set.
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * Add a role for identity.
     *
     * @param   string          $role                   Role to add.
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }

    /**
     * Return roles, the identity is member of.
     *
     * @return  array                                   Roles.
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Test if identity has a specific role.
     *
     * @param   string          $role                   Role to test.
     * @return  bool                                    Return true if identity has the role.
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }
}
