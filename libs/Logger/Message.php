<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Logger;

/**
 * Logger notification message.
 *
 * @copyright   copyright (c) 2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Message
{
    /**
     * Message.
     *
     * @type    string
     */
    protected $message;
    
    /**
     * Filename.
     *
     * @type    string
     */
    protected $file;
    
    /**
     * Linenumber.
     *
     * @type    int
     */
    protected $line;
    
    /**
     * Code.
     *
     * @type    int
     */
    protected $code;
    
    /**
     * Constructor.
     *
     * @param   string                  $message                    Message to set.
     * @param   string                  $file                       Optional filename to set.
     * @param   int                     $line                       Optional linenumber to set.
     * @param   int                     $code                       Optional error-code to set.
     */
    public function __construct($message, $file = null, $line = null, $code = null)
    {
        $this->message = $message;
        $this->file    = (!is_null($file) ? $file : '');
        $this->line    = (!is_null($line) ? $line : 0);
        $this->code    = (!is_null($code) ? $code : 0);
    }

    /**
     * Gets called if class instance is casted to a string.
     *
     * @return  string                                              Object representation as string.
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * Return message.
     *
     * @return  string                                              Message.
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Return file.
     *
     * @return  string                                              File.
     */
    public function getFile()
    {
        return $this->file;
    }
    /**
     * Return line.
     *
     * @return  string                                              Line.
     */
    public function getLine()
    {
        return $this->line;
    }
    /**
     * Return code.
     *
     * @return  string                                              Code.
     */
    public function getCode()
    {
        return $this->code;
    }
}
