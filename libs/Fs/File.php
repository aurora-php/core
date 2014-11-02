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
 * File object.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class File implements \IteratorAggregate
{
    /**
     * File handling flags:
     *
     * * T_READ_TRIM_NEWLINE -- Remove trailing newline characters.
     * * T_DELETE_ON_CLOSE -- Whether to delete file when object is deconstructed.
     */
    const T_READ_TRIM_NEWLINE =  1;
    const T_DELETE_ON_CLOSE   =  2;
    const T_FILE_ITERATOR     =  4;
    const T_STREAM_ITERATOR   = 12;

    /**
     * File opening mode.
     *
     * @type    string
     */
    private $open_mode = '';

    /**
     * File modes and read/write bit mapping:
     *
     * bit 1 - reading is allowed
     * bit 2 - writing is allowed
     *
     * @type    array
     */
    private static $modes = array(
        'r'  => 1, 'r+' => 3,
        'w'  => 2, 'w+' => 3,
        'a'  => 2, 'a+' => 3,
        'x'  => 2, 'x+' => 3,
        'c'  => 2, 'c+' => 3
    );

    /**
     * File handle.
     *
     * @type    resource
     */
    private $fh = null;

    /**
     * If reading from file is possible.
     *
     * @type    bool
     */
    private $can_read = false;

    /**
     * If writing to file is possible.
     *
     * @type    bool
     */
    private $can_write = false;

    /**
     * If file is opened in binary mode.
     *
     * @type    bool
     */
    private $is_binary = false;

    /**
     * File handling flags.
     *
     * @type    int
     */
    private $flags = 0;

    /**
     * Meta data available for the s
     *
     * @type    array
     */
    protected $meta = array();

    /**
     * Constructor. Takes either a name of file to read/write or a stream-resource. The
     * second parameter will be ignored, if the first parameter is a stream-resource. If
     * the first parameter is a string, it is considered to be a filename. The constructor
     * checks, if the file.
     *
     * @param   string|resource             $file           Stream resource or filename.
     * @param   string                      $open_mode      File open mode.
     * @param   int                         $flags          Additional flags to set.
     */
    public function __construct($file, $open_mode = 'r', $flags = 0)
    {
        if (is_resource($file)) {
            $this->meta = stream_get_meta_data($file);

            $this->setProperties($this->meta['mode']);

            $this->fh = $file;
        } elseif (is_string($file)) {
            $this->setProperties($open_mode);

            if (!($this->fh = @fopen($file, $open_mode))) {
                $info = error_get_last();

                throw new \Exception($info['message'], $info['type']);
            } else {
                $this->meta = stream_get_meta_data($this->fh);
            }
        }

        if (($flags & self::T_DELETE_ON_CLOSE) == self::T_DELETE_ON_CLOSE && !$this->isLocal()) {
            // remove 'delete on close' flag, if file is not local
            $flags = $flags ^ self::T_DELETE_ON_CLOSE;

            trigger_error("remote file cannot be deleted");
        }

        $this->flags = $flags;
    }

    /**
     * Destructor closes open file handle.
     */
    public function __destruct()
    {
        if (($this->flags & self::T_DELETE_ON_CLOSE) == self::T_DELETE_ON_CLOSE) {
            $path = parse_url($this->meta['uri'], PHP_URL_PATH);

            fclose($this->fh);

            if (file_exists($path)) {
                unlink($path);
            }
        } else {
            fclose($this->fh);
        }
    }

    /**
     * Return file URI if file object is casted to a string.
     *
     * @return  string                                      URI of file.
     */
    public function __toString()
    {
        return $this->meta['uri'];
    }

    /**
     * Return a new fileiterator instance for iterating file contents. Note, that this will open
     * another file handle to the file in read-only mode.
     *
     * @return  \Octris\Core\Fs\FileIterator            Instance of fileiterator.
     */
    public function getIterator()
    {
        if (($this->flags & self::T_STREAM_ITERATOR) == self::T_STREAM_ITERATOR) {
            $file = $this->fh;
        } else {
            $file = $this->meta['uri'];
        }

        return new \Octris\Core\Fs\FileIterator($file, $this->flags);
    }

    /**
     * Set file properties according to open mode: whether it's opened in binary mode or not,
     * where it's possible to read from and / or write to file.
     */
    private function setProperties($mode)
    {
        $tmp = $mode;

        if (strpos('bt', substr($mode, -1)) !== false) {
            $this->is_binary = true;
            $mode = substr($mode, 0, -1);
        }

        if (!isset(self::$modes[$mode])) {
            throw new \Exception("Invalid file mode '$tmp'");
        } else {
            $this->can_read  = (bool)(self::$modes[$mode] & 1);
            $this->can_write = (bool)(self::$modes[$mode] & 2);
        }
    }

    /**
     * Return file handle of resource.
     *
     * @return  resource                                    Handle of resource.
     */
    public function getHandle()
    {
        return $this->fh;
    }

    /**
     * Returns whether it is possible to read from the file.
     *
     * @return  bool                                        Returns true in case of reading from file is allowed.
     */
    public function canRead()
    {
        return $this->can_read;
    }

    /**
     * Returns whether it is possible to write to the file.
     *
     * @return  bool                                        Returns true in case of writing to file is allowed.
     */
    public function canWrite()
    {
        return $this->can_write;
    }

    /**
     * Set blocking mode for file.
     */
    public function setBlocking($mode)
    {
        stream_set_blocking($this->fh, $mode);
    }

    /**
     * Set a callback that will be called for every line read / written. The callback takes two parameters
     */
    public function setCallback(callable $callback)
    {

    }

    /**
     * Create a temporary file.
     */
    public static function createTempFile($prefix, $dir = null)
    {
        $file = tempnam((is_null($dir) ? sys_get_temp_dir() : $dir), $prefix);

        return new self($file, 'w', true);
    }

    /**
     * Test if a specified file exists.
     *
     * @param   string              $file                   File to test.
     * @return  bool                                        Returns true if file exists.
     */
    public static function isExist($file)
    {
        return file_exists($file);
    }

    /**
     * Check if file is local or not.
     *
     * @return  bool                                        Returns true if file is local.
     */
    public function isLocal()
    {
        return stream_is_local($this->fh);
    }

    /**
     * Test whether file is seekable.
     *
     * @return  bool                                        Returns true if file is seekable.
     */
    public function isSeekable()
    {
        return $this->meta['seekable'];
    }

    /**
     * Seek in a file if possible.
     *
     * @param   int                 $pos                    Position to seek to.
     * @return  bool                                        Returns true if seek was possible otherwise false.
     */
    public function seek($pos)
    {
        if (($return = $this->isSeekable())) {
            $return = (fseek($this->fh, $pos) === 0);
        }

        return $return;
    }

    /**
     * Rewind file pointer if possible.
     *
     * @return  bool                                        Returns true if rewind was possible otherwise false.
     */
    public function rewind()
    {
        if (($return = $this->isSeekable())) {
            $return = rewind($this->fh);
        }

        return $return;
    }

    /**
     * Read from file.
     *
     * @param   int                 $len                    Optional number of bytes to read from file.
     * @return  string                                      Read bytes.
     */
    public function read($len = null)
    {
        $row = fgets($this->fh, $len);

        if (($this->flags & self::T_READ_TRIM_NEWLINE) == self::T_READ_TRIM_NEWLINE) {
            rtrim($row, "\n\r");
        }

        return $row;
    }

    /**
     * Write to file.
     *
     * @param   string              $str                    String to write to file.
     * @param   int                 $len                    Optional maximum length of string to write.
     * @return  int|bool                                    Returns number of bytes written or 'false' if writing
     *                                                      was not possible.
     */
    public function write($str, $len = null)
    {
        if (is_null($len)) {
            $return = fwrite($this->fh, $str);
        } else {
            $return = fwrite($this->fh, $str, $len);
        }

        return $return;
    }

    /**
     * Test if end of file is reached.
     *
     * @return  bool                                        Returns true, if end of file is reached.
     */
    public function eof()
    {
        return feof($this->fh);
    }

    /**
     * Force writing of all buffered output to file.
     */
    public function flush()
    {
        fflush($this->fh);
    }

    /**
     * Perform file locking operation.
     *
     * @param   int                     $operation          Operation to perform.
     */
    public function lock($operation)
    {
        if (stream_supports_lock($this->fh)) {
            flock($this->fh, $operation);
        }
    }

    /**
     * Pass all file content to stdout.
     */
    public function passthru()
    {
        fpassthru($this->fh);
    }

    /**
     * Return information about a file.
     *
     * @return  array                                               File information.
     */
    public function stat()
    {
        return fstat($this->fh);
    }

    /**
     * Return position within a file.
     *
     * @return  int                                                 Position.
     */
    public function tell()
    {
        return ftell($this->fh);
    }

    /**
     * Cut file after the specified number of bytes.
     *
     * @param   int                         $size                   Number of bytes to cut file after.
     */
    public function truncate($size)
    {
        ftruncate($this->fh, $size);
    }

    /**
     * Return all content of the file as one string. Note that calling this method will reset the file
     * pointer.
     *
     * @return  string                                              Content of the file.
     */
    public function getContent()
    {
        return stream_get_contents($this->fh);
    }
}
