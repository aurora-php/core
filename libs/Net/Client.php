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
 * Generic cURL class.
 *
 * @copyright   Copyright (c) 2012-2013 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class Client
{
    /**
     * Supported schemes. Is empty, if there is no limitation for
     * protocols.
     *
     * @type    array
     */
    protected static $schemes = array();
    
    /**
     * Curl info.
     *
     * @type    array
     */
    protected static $info = null;
    
    /**
     * Options for curl client.
     *
     * @type    array
     */
    protected $options = array();
    
    /**
     * Information of the last request performed.
     *
     * @type    array
     */
    protected $request_info = array();
    
    /**
     * Session assigned to the client.
     *
     * @type    \octris\core\net|null
     */
    protected $session = null;
    
    /**
     * Event listener.
     *
     * @type    callable|null
     */
    protected $listener = null;
    
    /**
     * Client URI.
     *
     * @type    \octris\core\type\uri
     */
    protected $uri;
    
    /**
     * Constructor.
     *
     * @param   \Octris\Core\Type\Uri       $uri            URI
     */
    public function __construct(\Octris\Core\Type\Uri $uri)
    {
        if (!extension_loaded('curl')) {
            throw new \Exception('Missing ext/curl');
        }

        if (is_null(self::$info)) {
            self::$info = curl_version();
        }

        if (count(static::$schemes) > 0 && !in_array($uri->scheme, static::$schemes)) {
            throw new \Exception(sprintf(
                'Invalid URI specified, supported protocols are "%s"',
                implode(', ', static::$schemes)
            ));
        }

        $this->uri = clone $uri;

        $this->options[CURLOPT_RETURNTRANSFER] = true;
    }

    /**
     * Clone.
     *
     */
    public function __clone()
    {
        // cloned client instance is not part of a session
        $this->session = null;
    }

    /**
     * Return URL of client when class instance is casted to a string.
     *
     * @return  string                                          The URL of the client.
     */
    public function __toString()
    {
        return (string)$this->uri;
    }

    /**
     * Enable/disable verbose output.
     *
     * @param   bool                    $verbose                Whether to do verbose output or not.
     */
    public function setVerbose($verbose)
    {
        $this->options[CURLOPT_VERBOSE] = !!$verbose;
    }

    /**
     * Set timeout in seconds or microseconds (as float).
     *
     * @param   $timout             $timeout            The timeout to set.
     */
    public function setTimeout($timeout)
    {
        if (is_float($sec)) {
            unset($this->options[CURLOPT_CONNECTTIMEOUT]);
            $this->options[CURLOPT_CONNECTTIMEOUT_MS] = (int)($sec * 1000);
        } else {
            unset($this->options[CURLOPT_CONNECTTIMEOUT_MS]);
            $this->options[CURLOPT_CONNECTTIMEOUT] = $sec;
        }
    }

    /**
     * Return options set for client.
     *
     * @return  array                                   Curl options.
     */
    public function getOptions()
    {
        $this->options[CURLOPT_URL] = (string)$this->uri;

        return $this->options;
    }

    /**
     * Add curl client to a session.
     *
     * @param   \Octris\Core\Net        $sesstion   Session to assign to the client.
     */
    public function setSession(\Octris\Core\Net $session)
    {
        if (!is_null($this->session)) {
            throw new \Exception('Client is already assigned to a session');
        }

        $this->session = $session;
    }

    /**
     * Get session the client is assigned to.
     *
     * @return  \octris\core\net                    Session the client is assigned to.
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set event listener.
     *
     * @param   callable        $listener               Listener to set.
     */
    public function setListener(callable $listener)
    {
        $this->listener = $listener;
    }

    /**
     * Get event listener.
     *
     * @return  callable                                Listener set.
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * Get request information of the last executed request.
     *
     * @return  array                                   Request info.
     */
    public function getRequestInfo()
    {
        return $this->request_info;
    }

    /**
     * Execute client.
     *
     * @return  string                                  Response.
     */
    public function execute()
    {
        if (!is_null($this->session)) {
            throw new \Exception('Unable to execute a client that is assigned to a session');
        }

        $ch = curl_init();
        curl_setopt_array($ch, $this->getOptions());

        $return = curl_exec($ch);

        if (curl_errno($ch) && isset($this->options[CURLOPT_VERBOSE]) && $this->options[CURLOPT_VERBOSE]) {
            printf("curl-error #%d: %s\n", curl_errno($ch), curl_error($ch));
        }

        $this->request_info = curl_getinfo($ch);

        curl_close($ch);

        if (!is_null($this->listener)) {
            $cb = $this->listener;
            $cb($return);
        }

        return $return;
    }
}
