<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Auth\Storage;

/**
 * Storage handler for storing identity into session.
 *
 * @octdoc      c:storage/session
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Session implements \Octris\Core\Auth\Storage_if
{
    /**
     * Instance of session class.
     *
     * @octdoc  p:session/$session
     * @type    \octris\core\app\web\session
     */
    protected $session;
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:session/__construct
     */
    public function __construct()
    {
        $this->session = \Octris\Core\App\Web\Session::getInstance();
    }

    /**
     * Returns whether storage contains an identity or not.
     *
     * @octdoc  m:session/isEmpty
     * @return                                                  Returns true, if storage is empty.
     */
    public function isEmpty()
    {
        return (!$this->session->isExist('identity', __CLASS__));
    }

    /**
     * Store identity in storage.
     *
     * @octdoc  m:session/setIdentity
     * @param   \Octris\Core\Auth\Identity  $identity       Identity to store in storage.
     */
    public function setIdentity(\Octris\Core\Auth\Identity $identity)
    {
        $this->session->setValue('identity', base64_encode(serialize($identity)), __CLASS__);
    }

    /**
     * Return identity from storage.
     *
     * @octdoc  m:session/getIdentity
     * @return  \octris\core\auth\identity                  Identity stored in storage.
     */
    public function getIdentity()
    {
        return unserialize(base64_decode($this->session->getValue('identity', __CLASS__)));
    }

    /**
     * Deletes identity from storage.
     *
     * @octdoc  m:session/unsetIdentity
     */
    public function unsetIdentity()
    {
        $this->session->unsetValue('identity', __CLASS__);
    }
}
