<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Fs;

/**
 * Implements an iterator for a file.
 *
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Fileiterator implements \Iterator, \SeekableIterator
{
    /**
     * File handle.
     *
     * @type    resource
     */
    protected $fh = null;
    
    /**
     * File handling flags.
     *
     * @type    int
     */
    protected $flags = 0;
    
    /**
     * Current row number.
     *
     * @type    int
     */
    protected $row = null;
    
    /**
     * Contents of current line of file.
     *
     * @type    string
     */
    protected $current = '';
    
    /**
     * Whether file is seekable.
     *
     * @type    bool
     */
    protected $is_seekable;
    
    /**
     * Constructor.
     *
     * @param   string|resource             $file                       Stream resource or filename.
     * @param   int                         $flags                      Optional flags.
     */
    public function __construct($file, $flags = 0)
    {
        if (is_resource($file)) {
            $meta = stream_get_meta_data($file);

            $this->fh = $file;

            $this->is_seekable = $meta['seekable'];
            $this->flags       = $flags;
        } elseif (!($this->fh = @fopen($uri, 'r'))) {
            $info = error_get_last();

            throw new \Exception($info['message'], $info['type']);
        } else {
            $meta = stream_get_meta_data($this->fh);

            $this->is_seekable = $meta['seekable'];
            $this->flags       = $flags;
        }
    }

    /**
     * Return current row of file.
     *
     * @return  string                                                  Current row of file.
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Return number of current row.
     *
     * @return  int                                                     Number of current row.
     */
    public function key()
    {
        return $this->row;
    }

    /**
     * Rewind file to beginning.
     *
     */
    public function rewind()
    {
        rewind($this->fh);

        $this->row = null;
        $this->next();
    }

    /**
     * Fetch next row.
     *
     */
    public function next()
    {
        if (!feof($this->fh)) {
            $this->current = fgets($this->fh);

            if (($this->flags & \Octris\Core\Fs\File::T_READ_TRIM_NEWLINE) == \Octris\Core\Fs\File::T_READ_TRIM_NEWLINE) {
                $this->current = rtrim($this->current, "\n\r");
            }

            $this->row = (is_null($this->row) ? 1 : ++$this->row);
        }
    }

    /**
     * Check if eof is reached.
     *
     * @return  bool                                                    Returns true, if eof is not reached.
     */
    public function valid()
    {
        return !feof($this->fh);
    }

    /**
     * Seek file to specified row number.
     *
     * @param   int                             $row                    Number of row to seek to.
     */
    public function seek($row)
    {
        if (!$this->is_seekable) {
            trigger_error("file is not seekable");
        } elseif ($row != $this->row) {
            if ($row < $this->row) {
                // absolute seek
                $start = 0;
                rewind($this->fh);
            } else {
                // relative seek
                $start = $this->row;
            }

            for ($i = $start; $i < $row && !feof($this->fh); ++$i) {
                ++$this->row;
                fgets($this->fh);
            }

            if (!feof($this->fh)) {
                $this->next();
            }
        }
    }
}
