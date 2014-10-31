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
 * ACL Resource.
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Resource
{
    /**
     * Name of resource.
     *
     * @type    string
     */
    protected $name;
    
    /**
     * Default policy for resource.
     *
     * @type    int
     */
    protected $policy = \Octris\Core\Auth\Acl::T_ALLOW;
    
    /**
     * Actions available for resource.
     *
     * @type    array
     */
    protected $actions = array();
    
    /**
     * Constructor.
     *
     * @param   string          $name                   Name of resource.
     * @param   array           $actions                Actions to configure for resource.
     */
    public function __construct($name, array $actions)
    {
        $this->name    = $name;
        $this->actions = $actions;
    }

    /**
     * Set default policy for resource.
     *
     * @param   int             $policy                 Policy to set.
     */
    public function setPolicy($policy)
    {
        if ($policy != \Octris\Core\Auth\Acl::T_ALLOW && $policy != \Octris\Core\Auth\Acl::T_DENY) {
            throw new \Exception('policy needs to be either acl::T_ALLOW or acl::T_DENY');
        }

        $this->policy = $policy;
    }

    /**
     * Return the default policy of the resource.
     *
     * @return  int                                     Default policy.
     */
    public function getPolicy()
    {
        return $this->policy;
    }

    /**
     * Returns name of resource.
     *
     * @return  string                                  Name of resource.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Test if resource has a specified action.
     *
     * @param   string          $action                 Name of action to test.
     * @return  bool                                    Returns true if action is known.
     */
    public function hasAction($action)
    {
        return in_array($action, $this->actions);
    }
}
