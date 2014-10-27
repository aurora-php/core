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
 * Non persistent storage of identity. This is the default authentication
 * storage handler.
 *
 * @octdoc      c:storage/transient
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Transient implements \Octris\Core\Auth\Storage_if
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
    {
    }

    /**
     * Returns whether storage contains an identity or not.
     *
     * @octdoc  m:storage_if/isEmpty
     * @return                                                  Returns true, if storage is empty.
     */
    public function isEmpty()
    {
        return (empty($this->identity));
    }

    /**
     * Store identity in storage.
     *
     * @octdoc  m:transient/setIdentity
     * @param   \Octris\Core\Auth\Identity  $identity       Identity to store in storage.
     */
    public function setIdentity(\Octris\Core\Auth\Identity $identity)
    {
        $this->identity = $identity;
    }

    /**
     * Return identity from storage.
     *
     * @octdoc  m:transient/getIdentity
     * @return  \octris\core\auth\identity                  Identity stored in storage.
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Deletes identity from storage.
     *
     * @octdoc  m:transient/unsetIdentity
     */
    public function unsetIdentity()
    {
        $this->identity = null;
    }
}
