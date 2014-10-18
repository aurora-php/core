<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth\storage {
    /**
     * Non persistent storage of identity. This is the default authentication
     * storage handler.
     *
     * @octdoc      c:storage/transient
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class transient implements \org\octris\core\auth\storage_if
    /**/
    {
        /**
         * Transient identity storage.
         *
         * @octdoc  p:transient/$identity
         * @type    array|null
         */
        protected $identity = null;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:transient/__construct
         */
        public function __construct()
        /**/
        {
        }

        /**
         * Returns whether storage contains an identity or not.
         *
         * @octdoc  m:storage_if/isEmpty
         * @return                                                  Returns true, if storage is empty.
         */
        public function isEmpty()
        /**/
        {
            return (empty($this->identity));
        }

        /**
         * Store identity in storage.
         *
         * @octdoc  m:transient/setIdentity
         * @param   \org\octris\core\auth\identity  $identity       Identity to store in storage.
         */
        public function setIdentity(\org\octris\core\auth\identity $identity)
        /**/
        {
            $this->identity = $identity;
        }

        /**
         * Return identity from storage.
         *
         * @octdoc  m:transient/getIdentity
         * @return  \org\octris\core\auth\identity                  Identity stored in storage.
         */
        public function getIdentity()
        /**/
        {
            return $this->identity;
        }

        /**
         * Deletes identity from storage.
         *
         * @octdoc  m:transient/unsetIdentity
         */
        public function unsetIdentity()
        /**/
        {
            $this->identity = null;
        }
    }
}
