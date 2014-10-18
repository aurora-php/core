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
     * Class for storing authenticated identity.
     *
     * @octdoc      c:auth/identity
     * @copyright   copyright (c) 2011-2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class identity
    /**/
    {
        /**
         * Authentication status code.
         *
         * @octdoc  p:identity/$code
         * @type    int
         */
        protected $code;
        /**/

        /**
         * Properties stored in the identity.
         *
         * @octdoc  p:identity/$identity
         * @type    array
         */
        protected $identity = array();
        /**/

        /**
         * Roles assigned to the identity.
         *
         * @octdoc  p:identity/$roles
         * @type    array
         */
        protected $roles = array('guest');
        /**/

        /**
         * Construct.
         *
         * @octdoc  m:identity/__construct
         * @param   int             $code                   Status code.
         * @param   array           $identity               Settings, that are stored in the identity.
         */
        public function __construct($code, array $identity)
        /**/
        {
            $this->code     = $code;
            $this->identity = $identity;
        }

        /**
         * Method is called, when identity object get's serialized, for example when it's saved in the
         * storage.
         *
         * @octdoc  m:identity/__sleep
         * @return  array                                   Field names to serialize.
         */
        public function __sleep()
        /**/
        {
            return array('code', 'identity', 'roles');
        }

        /**
         * Returns true, if identity is valid.
         *
         * @octdoc  m:identity/isValid
         * @param   bool                                    Identity validation status.
         */
        public function isValid()
        /**/
        {
            return ($this->code === \org\octris\core\auth::T_AUTH_SUCCESS);
        }

        /**
         * Return status code of identity authentication.
         *
         * @octdoc  m:identity/getCode
         * @param   int                                     Status code.
         */
        public function getCode()
        /**/
        {
            return $this->code;
        }

        /**
         * Returns the stored identity data.
         *
         * @octdoc  m:identity/getIdentity
         * @param   array                                   Identity data.
         */
        public function getIdentity()
        /**/
        {
            return $this->identity;
        }

        /**
         * Set roles for identity.
         *
         * @octdoc  m:identity/setRoles
         * @param   array           $roles                  Roles to set.
         */
        public function setRoles(array $roles)
        /**/
        {
            $this->roles = $roles;
        }

        /**
         * Add a role for identity.
         *
         * @octdoc  m:identity/addRole
         * @param   string          $role                   Role to add.
         */
        public function addRole($role)
        /**/
        {
            $this->roles[] = $role;
        }

        /**
         * Return roles, the identity is member of.
         *
         * @octdoc  m:identity/getRoles
         * @return  array                                   Roles.
         */
        public function getRoles()
        /**/
        {
            return $this->roles;
        }
    }
}
