<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Auth\Adapter;

/**
 * Allows authentication against a htpasswd file. The encryptions supported
 * are SHA1 and crypt. This class (currently) does not(!) support
 * plain-text passwords.
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Htpasswd implements \Octris\Core\Auth\IAdapter
{
    /**
     * Username to authenticate with adapter.
     *
     * @type    string
     */
    protected $username = '';

    /**
     * Credential to authenticate with adapter.
     *
     * @type    string
     */
    protected $credential = '';

    /**
     * Htpasswd file to use for authentication.
     *
     * @type    string
     */
    protected $file;

    /**
     * Constructor.
     *
     * @param   string          $file               Htpasswd file to use for authentication.
     */
    public function __construct($file)
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new \Exception(sprintf('File not found or file is not readable "%s"', $file));
        }

        $this->file = $file;
    }

    /**
     * Set's a username to be authenticated.
     *
     * @param   string          $username           Username to authenticate.
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Set's a credential to be authenticated.
     *
     * @param   string          $credential         Credential to authenticate.
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
    }

    /**
     * Authenticate.
     *
     * @return  \Octris\Core\Auth\Identity                  Instance of identity class.
     */
    public function authenticate()
    {
        $result = \Octris\Core\Auth::AUTH_FAILURE;

        if (empty($this->username)) {
            throw new \Exception('Username cannot be empty');
        }
        if (empty($this->credential)) {
            throw new \Exception('Credential cannot be empty');
        }

        if (!($fp = fopen($this->file, 'r'))) {
            throw new \Exception(sprintf('Unable to read file "%s"', $this->file));
        } else {
            $result = \Octris\Core\Auth::IDENTITY_UNKNOWN;

            while (!feof($fp)) {
                if ((list($username, $password) = fgetcsv($fp, 512, ':')) && $username == $this->username) {
                    if ($result != \Octris\Core\Auth::IDENTITY_UNKNOWN) {
                        $result = \Octris\Core\Auth::IDENTITY_AMBIGUOUS;
                        break;
                    } else {
                        if (preg_match('/^\{SHA\}(.+)$/', $password, $match)) {
                            $test     = base64_encode(sha1($this->credential, true));
                            $password = $match[1];
                        } else {
                            $test = crypt($this->credential, substr($password, 0, 3));
                        }

                        if ($test === $password) {
                            $result = \Octris\Core\Auth::AUTH_SUCCESS;
                        } else {
                            $result = \Octris\Core\Auth::CREDENTIAL_INVALID;
                        }
                    }
                }
            }

            fclose($fp);
        }

        return new \Octris\Core\Auth\Identity(
            $result,
            array(
                'username' => $this->username
            )
        );
    }
}
