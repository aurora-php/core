<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Type\Collection;

/**
 * Subcollection of a collection. This class is only ment to be instantiated by it's parent
 * class. The differences are:
 *
 * * requires an array as parameter
 * * the parameter is passed and stored by reference
 * * the parameter will not be normalizd
 *
 * @copyright   copyright (c) 2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Subcollection extends \Octris\Core\Type\Collection
{
    /**
     * Constructor.
     *
     * @param   array               &$data              Sub-array of the parent collection.
     */
    public function __construct(array &$data)
    {
        $this->data =& $data;
    }
}
