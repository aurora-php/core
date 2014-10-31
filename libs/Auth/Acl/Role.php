<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Auth\Acl;

/**
 * ACL role.
 *
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Role
{
    /**
     * Name of role.
     *
     * @type    string
     */
    protected $name;
    
    /**
     * Additional policies assigned to role.
     *
     * @type    array
     */
    protected $policies = array();
    
    /**
     * Parent roles.
     *
     * @type    array
     */
    protected $parents = array();
    
    /**
     * Constructor.
     *
     * @param   string                          $name       The name of the role.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Return name of role, when casted to a string.
     *
     * @return  string                                      Name of role.
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Add a parent role.
     *
     * @param   \Octris\Core\Auth\Acl\Role  $parent     Role to inherit.
     */
    public function addParent(\Octris\Core\Auth\Acl\Role $parent)
    {
        $this->parents[] = $parent;
    }

    /**
     * Add the policy for a resource to the role.
     *
     * @param   \Octris\Core\Auth\Acl\Resource  $resource       Resource to add policy for.
     * @param   string                              $action         Action to add policy for.
     * @param   int                                 $policy         Policy to set.
     */
    public function addPolicy(\Octris\Core\Auth\Acl\Resource $resource, $action, $policy)
    {
        $name = $resource->getName();

        if (!isset($this->policies[$name])) {
            $this->policies[$name] = array(
                'resource' => $resource,
                'actions'  => array()
            );
        }

        $this->policies[$name]['actions'][$action] = $policy;
    }

    /**
     * Return the role-specific policy for a specified resource and action. This method will not
     * return the default policy of a resource, instead -- if no policy for the resource is
     * specified in the role -- the method will return 'null'.
     *
     * @param   \Octris\Core\Auth\Acl\Resource  $resource       Resource to get policy of.
     * @param   string                              $action         Action to get policy of.
     * @return  int|null                                            Policy, if available
     */
    public function getPolicy(\Octris\Core\Auth\Acl\Resource $resource, $action)
    {
        $name = $resource->getName();

        return (isset($this->policies[$name]) &&
                isset($this->policies[$name]['actions'][$action])
                ? $this->policies[$name]['actions'][$action]
                : null);
    }

    /**
     * Thie method will calculate the policy for a specified resource and action by
     * including the parent roles, if any, to determine the final policy.
     *
     * @param   \Octris\Core\Auth\Acl\Resource  $resource       Resource to get policy of.
     * @param   string                              $action         Action to get policy of.
     * @param   int                                 $default        Optional default policy.
     * @return  int|null                                            Policy, if available
     */
    public function calcPolicy(\Octris\Core\Auth\Acl\Resource $resource, $action, $default = null)
    {
        $name   = $resource->getName();
        $result = (is_null($default) ? $resource->getPolicy() : $default);

        if (isset($this->policies[$name]) && isset($this->policies[$name]['actions'][$action])) {
            $result = self::$this->policies[$name]['actions'][$action];
        } else {
            foreach ($this->parents as $parent) {
                $result = $parent->calcPolicy($resource, $action, $default);
            }
        }

        return $result;
    }

    /**
     * Test whether a role may perform an specified action on a specified resource.
     *
     * @param   \Octris\Core\Auth\Acl\Resource  $resource       Resource to test.
     * @param   string                              $action         Action to test.
     * @return  bool                                                Returns true, if role is authorized.
     */
    public function isAuthorized(\Octris\Core\Auth\Acl\Resource $resource, $action)
    {
        $policy = $this->calcPolicy($resource, $action);

        return ($policy == \Octris\Core\Auth\Acl::T_ALLOW);
    }
}
