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
     * Interface for building identity storage handlers.
     *
     * @octdoc      i:auth/storage_if
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface storage_if 
    /**/
    {
        /**
         * Returns whether storage contains an identity or not.
         *
         * @octdoc  m:storage_if/isEmpty
         * @return                                                  Returns true, if storage is empty.
         */
        public function isEmpty();
        /**/

        /**
         * Store identity in storage.
         *
         * @octdoc  m:storage_if/setIdentity
         * @param   \org\octris\core\auth\identity  $identity       Identity to store in storage.
         */
        public function setIdentity(\org\octris\core\auth\identity $identity);
        /**/

        /**
         * Return identity from storage.
         *
         * @octdoc  m:storage_if/getIdentity
         * @return  \org\octris\core\auth\identity                  Identity stored in storage.
         */
        public function getIdentity();
        /**/

        /**
         * Deletes identity from storage.
         *
         * @octdoc  m:storage_if/unsetIdentity
         */
        public function unsetIdentity();
        /**/
    }
}
