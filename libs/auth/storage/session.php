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
     * Storage handler for storing identity into session.
     *
     * @octdoc      c:storage/session
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class session implements \org\octris\core\auth\storage_if
    /**/
    {
        /**
         * Instance of session class.
         *
         * @octdoc  p:session/$session
         * @type    \org\octris\core\app\web\session
         */
        protected $session;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:session/__construct
         */
        public function __construct()
        /**/
        {
            $this->session = \org\octris\core\app\web\session::getInstance();
        }

        /**
         * Returns whether storage contains an identity or not.
         *
         * @octdoc  m:session/isEmpty
         * @return                                                  Returns true, if storage is empty.
         */
        public function isEmpty()
        /**/
        {
            return (!$this->session->isExist('identity', __CLASS__));
        }

        /**
         * Store identity in storage.
         *
         * @octdoc  m:session/setIdentity
         * @param   \org\octris\core\auth\identity  $identity       Identity to store in storage.
         */
        public function setIdentity(\org\octris\core\auth\identity $identity)
        /**/
        {
            $this->session->setValue('identity', base64_encode(serialize($identity)), __CLASS__);
        }

        /**
         * Return identity from storage.
         *
         * @octdoc  m:session/getIdentity
         * @return  \org\octris\core\auth\identity                  Identity stored in storage.
         */
        public function getIdentity()
        /**/
        {
            return unserialize(base64_decode($this->session->getValue('identity', __CLASS__)));
        }

        /**
         * Deletes identity from storage.
         *
         * @octdoc  m:session/unsetIdentity
         */
        public function unsetIdentity()
        /**/
        {
            $this->session->unsetValue('identity', __CLASS__);
        }
    }
}
