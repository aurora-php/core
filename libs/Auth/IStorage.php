<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Auth;

/**
 * Interface for building identity storage handlers.
 *
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface IStorage
{
    /**
     * Returns whether storage contains an identity or not.
     *
     * @return                                                  Returns true, if storage is empty.
     */
    public function isEmpty();
    
    /**
     * Store identity in storage.
     *
     * @param   \Octris\Core\Auth\Identity  $identity       Identity to store in storage.
     */
    public function setIdentity(\Octris\Core\Auth\Identity $identity);
    
    /**
     * Return identity from storage.
     *
     * @return  \octris\core\auth\identity                  Identity stored in storage.
     */
    public function getIdentity();
    
    /**
     * Deletes identity from storage.
     *
     */
    public function unsetIdentity();
    /**/
}
