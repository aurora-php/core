<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\logger;

/**
 * Logger notification message.
 *
 * @octdoc      c:logger/message
 * @copyright   copyright (c) 2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class message
{
    /**
     * Message.
     *
     * @octdoc  p:message/$message
     * @type    string
     */
    protected $message;
    /**/

    /**
     * Filename.
     *
     * @octdoc  p:message/$file
     * @type    string
     */
    protected $file;
    /**/

    /**
     * Linenumber.
     *
     * @octdoc  p:message/$line
     * @type    int
     */
    protected $line;
    /**/

    /**
     * Code.
     *
     * @octdoc  p:message/$code
     * @type    int
     */
    protected $code;
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:message/__construct
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
     * @octdoc  m:message/__toString
     * @return  string                                              Object representation as string.
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * Return message.
     *
     * @octdoc  m:message/getMessage
     * @return  string                                              Message.
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Return file.
     *
     * @octdoc  m:message/getFile
     * @return  string                                              File.
     */
    public function getFile()
    {
        return $this->file;
    }
    /**
     * Return line.
     *
     * @octdoc  m:message/getLine
     * @return  string                                              Line.
     */
    public function getLine()
    {
        return $this->line;
    }
    /**
     * Return code.
     *
     * @octdoc  m:message/getCode
     * @return  string                                              Code.
     */
    public function getCode()
    {
        return $this->code;
    }
}

