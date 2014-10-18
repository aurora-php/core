<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth\acl {
    /**
     * ACL Resource.
     *
     * @octdoc      c:acl/resource
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class resource 
    /**/
    {
        /**
         * Name of resource.
         *
         * @octdoc  p:resource/$name
         * @type    string
         */
        protected $name;
        /**/

        /**
         * Default policy for resource.
         *
         * @octdoc  p:resource/$policy
         * @type    int
         */
        protected $policy = \org\octris\core\auth\acl::T_ALLOW;
        /**/

        /**
         * Actions available for resource.
         *
         * @octdoc  p:resource/$actions
         * @type    array
         */
        protected $actions = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:resource/__construct
         * @param   string          $name                   Name of resource.
         * @param   array           $actions                Actions to configure for resource.
         */
        public function __construct($name, array $actions)
        /**/
        {
            $this->name    = $name;
            $this->actions = $actions;
        }

        /**
         * Set default policy for resource.
         *
         * @octdoc  m:resource/setPolicy
         * @param   int             $policy                 Policy to set.
         */
        public function setPolicy($policy)
        /**/
        {
            if ($policy != \org\octris\core\auth\acl::T_ALLOW && $policy != \org\octris\core\auth\acl::T_DENY) {
                throw new \Exception('policy needs to be either acl::T_ALLOW or acl::T_DENY');
            }

            $this->policy = $policy;
        }

        /**
         * Return the default policy of the resource.
         *
         * @octdoc  m:resource/getPolicy
         * @return  int                                     Default policy.
         */
        public function getPolicy()
        /**/
        {
            return $this->policy;
        }

        /**
         * Returns name of resource.
         *
         * @octdoc  m:resource/getName
         * @return  string                                  Name of resource.
         */
        public function getName()
        /**/
        {
            return $this->name;
        }

        /**
         * Test if resource has a specified action.
         *
         * @octdoc  m:resource/hasAction
         * @param   string          $action                 Name of action to test.
         * @return  bool                                    Returns true if action is known.
         */
        public function hasAction($action)
        /**/
        {
            return in_array($action, $this->actions);
        }
    }
}
