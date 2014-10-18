<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth {
    /**
     * Simple implementation of access control lists.
     *
     * @octdoc      c:app/acl
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class acl
    /**/
    {
        /**
         * Policies.
         *
         * @octdoc  d:acl/T_ALLOW, T_DENY
         */
        const T_ALLOW = 1;
        const T_DENY  = 2;
        /**/

        /**
         * Configured access control lists.
         *
         * @octdoc  p:acl/$resources
         * @type    array
         */
        protected $resources = array();
        /**/

        /**
         * Roles configured in ACL.
         *
         * @octdoc  p:acl/$roles
         * @type    array
         */
        protected $roles = array();
        /**/

        /**
         * Instance of authentication library.
         *
         * @octdoc  p:acl/$auth
         * @type    \org\octris\core\auth|null
         */
        protected $auth = null;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:acl/__construct
         */
        public function __construct()
        /**/
        {
        }

        /**
         * Return array with properties to serialize.
         *
         * @octdoc  m:acl/__sleep
         * @return  array                                   Properties to serialize.
         */
        public function __sleep()
        /**/
        {
            return array('resources', 'roles');
        }

        /**
         * Load an ACL configuration and return an instance of acl class for this
         * configuration.
         *
         * @octdoc  m:acl/load
         * @param   string                  $file           File to load configuration from.
         */
        public static function load($file)
        /**/
        {
            if (!is_readable($file)) {
                throw new \Exception(sprintf("'%' is not readable", $file));
            }

            if (is_null($cfg = yaml_parse_file($file))) {
                throw new \Exception(sprintf("unable to load file '%s'", $file));
            }

            // TODO: validate with validation schema

            // build ACL
            $acl = new static();

            // build roles
            $roles = array();

            foreach ($cfg['roles'] as $role) {
                if (!is_array($role)) continue;

                $name = key($role);

                if (isset($roles[$name])) {
                    throw new \Exception(sprintf("role '%s' already taken", $name));
                }

                $roles[$name] = $acl->addRole($name);

                if (is_array($role[$name])) {
                    foreach ($role[$name] as $parent) {
                        if (!isset($roles[$parent])) {
                            throw new \Exception(sprintf("unable to inherit from unknown role '%s'", $parent));
                        }

                        $roles[$name]->addParent($roles[$parent]);
                    }
                }
            }

            // build resources
            $resources = array();

            foreach ($cfg['resources'] as $resource) {
                if (!is_array($resource)) continue;

                $name = key($resource);

                if (isset($resources[$name])) {
                    throw new \Exception(sprintf("resource '%s' is already defined", $name));
                }

                $resources[$name] = $acl->addResource($name, (is_array($resource[$name]) ? $resource[$name] : array()));
            }

            // build policies
            foreach ($cfg['policies'] as $resource) {
                if (!is_array($resource)) continue;

                $name = key($resource);

                if (!isset($resources[$name])) {
                    throw new \Exception(sprintf("unknown resource '%s'", $name));
                }

                if (!is_array($resource[$name])) {
                    continue;
                }

                if (isset($resource[$name]['default'])) {
                    $policy = strtoupper($resource[$name]['default']);

                    switch ($policy) {
                    case 'ALLOW':
                        $resources[$name]->setPolicy(self::T_ALLOW);
                        break;
                    case 'DENY':
                        $resources[$name]->setPolicy(self::T_DENY);
                        break;
                    default:
                        throw new \Exception(sprintf("unknow policy type '%s'", $policy));
                    }
                }

                if (isset($resource[$name]['actions']) && is_array($resource[$name]['actions'])) {
                    foreach ($resource[$name]['actions'] as $action) {
                        if (!is_array($action)) continue;

                        $action_name = key($action);

                        if (!$resources[$name]->hasAction($action_name)) {
                            throw new \Exception(sprintf("unknown action '%s'", $action_name));
                        }

                        if (!is_array($action[$action_name])) {
                            continue;
                        }

                        foreach ($action[$action_name] as $role => $policy) {
                            if (!isset($roles[$role])) {
                                throw new \Exception(sprintf("unknown role '%s'", $role));
                            }

                            switch ($policy) {
                            case 'ALLOW':
                                $roles[$role]->addPolicy(
                                    $resources[$name],
                                    $action_name,
                                    self::T_ALLOW
                                );
                                break;
                            case 'DENY':
                                $roles[$role]->addPolicy(
                                    $resources[$name],
                                    $action_name,
                                    self::T_DENY
                                );
                                break;
                            default:
                                throw new \Exception(sprintf("unknow policy type '%s'", $policy));
                            }
                        }
                    }
                }
            }

            return $acl;
        }

        /**
         * Set instance of authentication class to use in combination with ACL.
         *
         * @octdoc  m:acl/setAuthentication
         * @param   \org\octris\core\auth   $auth           Instance of authentication library.
         */
        public function setAuthentication(\org\octris\core\auth $auth)
        /**/
        {
            $this->auth = $auth;
        }

        /**
         * Add a resource to the ACL. In context of the octris framework a resource is
         * a PHP namespace or a PHP class name, typically the namespace of a module or
         * the class name of a page. The actions array can be for example the next
         * actions allowed for a page specified as resource.
         *
         * @octdoc  m:acl/addResource
         * @param   string                              $name       Name of resource to add.
         * @param   array                               $actions    Actions the resource can perform.
         * @return  \org\octris\core\auth\acl\resource              Instance of an ACL resource.
         */
        public function addResource($name, array $actions)
        /**/
        {
            return $this->resources[$name] = new \org\octris\core\auth\acl\resource($name, $actions);
        }

        /**
         * Add a role to the ACL.
         *
         * @octdoc  m:acl/addRole
         * @param   string                              $name       Name of the role.
         * @return  \org\octris\core\auth\acl\role                  Instance of an ACL role.
         */
        public function addRole($name)
        /**/
        {
            return $this->roles[$name] = new \org\octris\core\auth\acl\role($name);
        }

        /**
         * Test whether an identity is authorized for some action on a resource.
         *
         * @octdoc  m:acl/isAuthorized
         * @param   string          $resource               Name of resource.
         * @param   string          $action                 Name of action.
         */
        public function isAuthorized($resource, $action)
        /**/
        {
            $return = false;

            do {
                if (!isset($this->resources[$resource])) {
                    throw new \Exception(sprintf("unknown resource '%s'", $resource));
                }

                if (!$this->resources[$resource]->hasAction($action)) {
                    throw new \Exception(sprintf("unknown action '%s' for resource '%s'", $action, $resource));
                }

                if (is_null($this->auth)) {
                    // no authentication instance available
                    break;
                }

                if (!($identity = $this->auth->getIdentity())) {
                    // no identity in authentication object available
                    break;
                }

                $roles  = $identity->getRoles();

                foreach ($roles as $role) {
                    if (!isset($this->roles[$role])) {
                        throw new \Exception(sprintf("unknown role '%s'", $role));
                    }

                    if (($return = $this->roles[$role]->isAuthorized($this->resources[$resource], $action))) {
                        break;
                    }
                }
            } while(false);

            return $return;
        }
    }
}
