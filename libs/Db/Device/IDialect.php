<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Db\Device;

/**
 * Interface for implementing SQL dialects.
 *
 * @copyright   copyright (c) 2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface IDialect
{
    /**
     * Return LIMIT string.
     * 
     * @param   int             $limit                          Limit rows.
     * @param   int             $offset                         Optional offset.
     */
    public function getLimitString($limit, $offset = null);
}
