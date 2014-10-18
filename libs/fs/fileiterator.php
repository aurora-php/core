<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\fs {
    /**
     * Implements an iterator for a file.
     *
     * @octdoc      c:type/fileiterator
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class fileiterator implements \Iterator, \SeekableIterator
    /**/
    {
        /**
         * File handle.
         *
         * @octdoc  p:fileiterator/$fh
         * @type    resource
         */
        protected $fh = null;
        /**/
        
        /**
         * File handling flags.
         * 
         * @octdoc  p:fileiterator/$flags
         * @type    int
         */
        protected $flags = 0;
        /**/

        /**
         * Current row number.
         *
         * @octdoc  p:fileiterator/$row
         * @type    int
         */
        protected $row = null;
        /**/

        /**
         * Contents of current line of file.
         *
         * @octdoc  p:fileiterator/$current
         * @type    string
         */
        protected $current = '';
        /**/
        
        /**
         * Whether file is seekable.
         *
         * @octdoc  p:fileiterator/$is_seekable
         * @type    bool
         */
        protected $is_seekable;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  p:fileiterator/__construct
         * @param   string|resource             $file                       Stream resource or filename.
         * @param   int                         $flags                      Optional flags.
         */
        public function __construct($file, $flags = 0)
        /**/
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
         * @octdoc  m:fileiterator/current
         * @return  string                                                  Current row of file.
         */
        public function current()
        /**/
        {
            return $this->current;
        }

        /**
         * Return number of current row.
         *
         * @octdoc  m:fileiterator/key
         * @return  int                                                     Number of current row.
         */
        public function key()
        /**/
        {
            return $this->row;
        }

        /**
         * Rewind file to beginning.
         *
         * @octdoc  m:fileiterator/rewind
         */
        public function rewind()
        /**/
        {
            rewind($this->fh);

            $this->row = null;
            $this->next();
        }

        /**
         * Fetch next row.
         *
         * @octdoc  m:fileiterator/next
         */
        public function next()
        /**/
        {
            if (!feof($this->fh)) {
                $this->current = fgets($this->fh);

                if (($this->flags & \org\octris\core\fs\file::T_READ_TRIM_NEWLINE) == \org\octris\core\fs\file::T_READ_TRIM_NEWLINE) {
                    $this->current = rtrim($this->current, "\n\r");
                }

                $this->row = (is_null($this->row) ? 1 : ++$this->row);
            }
        }

        /**
         * Check if eof is reached.
         *
         * @octdoc  m:fileiterator/valid
         * @return  bool                                                    Returns true, if eof is not reached.
         */
        public function valid()
        /**/
        {
            return !feof($this->fh);
        }

        /**
         * Seek file to specified row number.
         *
         * @octdoc  m:fileiterator/seek
         * @param   int                             $row                    Number of row to seek to.
         */
        public function seek($row)
        /**/
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
}
