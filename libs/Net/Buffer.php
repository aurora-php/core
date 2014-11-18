<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Net;

/**
 * Helper class for temporarly storing request output data.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Buffer extends \Octris\Core\Fs\File
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'php://memory',
            'w',
            parent::READ_TRIM_NEWLINE | parent::STREAM_ITERATOR
        );
    }
}
