<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth\adapter {
    /**
     * Allows authentication against a htpasswd file. The encryptions supported
     * are SHA1 and crypt. This class (currently) does not(!) support
     * plain-text passwords. 
     *
     * @octdoc      c:adapter/htpasswd
     * @copyright   copyright (c) 2011-2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class htpasswd implements \org\octris\core\auth\adapter_if
    /**/
    {
        /**
         * Username to authenticate with adapter.
         *
         * @octdoc  p:htpasswd/$username
         * @type    string
         */
        protected $username = '';
        /**/

        /**
         * Credential to authenticate with adapter.
         *
         * @octdoc  p:htpasswd/$credential
         * @type    string
         */
        protected $credential = '';
        /**/

        /**
         * Htpasswd file to use for authentication.
         *
         * @octdoc  p:htpasswd/$file
         * @type    string
         */
        protected $file;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:htpasswd/__construct
         * @param   string          $file               Htpasswd file to use for authentication.
         */
        public function __construct($file)
        /**/
        {
            if (!is_file($file) || !is_readable($file)) {
                throw new \Exception(sprintf('File not found or file is not readable "%s"', $file));
            }

            $this->file = $file;
        }

        /**
         * Set's a username to be authenticated.
         *
         * @octdoc  m:htpasswd/setUsername
         * @param   string          $username           Username to authenticate.
         */
        public function setUsername($username)
        /**/
        {
            $this->username = $username;
        }

        /**
         * Set's a credential to be authenticated.
         *
         * @octdoc  m:htpasswd/setCredential
         * @param   string          $credential         Credential to authenticate.
         */
        public function setCredential($credential)
        /**/
        {
            $this->credential = $credential;
        }

        /**
         * Authenticate.
         *
         * @octdoc  m:htpasswd/authenticate
         * @return  \org\octris\core\auth\identity                  Instance of identity class.
         */
        public function authenticate()
        /**/
        {
            $result = \org\octris\core\auth::T_AUTH_FAILURE;

            if (empty($this->username)) {
                throw new \Exception('Username cannot be empty');
            }
            if (empty($this->credential)) {
                throw new \Exception('Credential cannot be empty');
            }

            if (!($fp = fopen($this->file, 'r'))) {
                throw new \Exception(sprintf('Unable to read file "%s"', $this->file));
            } else {
                $result = \org\octris\core\auth::T_IDENTITY_UNKNOWN;

                while (!feof($fp)) {
                    if ((list($username, $password) = fgetcsv($fp, 512, ':')) && $username == $this->username) {
                        if ($result != \org\octris\core\auth::T_IDENTITY_UNKNOWN) {
                            $result = \org\octris\core\auth::T_IDENTITY_AMBIGUOUS;
                            break;
                        } else {
                            if (preg_match('/^\{SHA\}(.+)$/', $password, $match)) {
                                $test     = base64_encode(sha1($this->credential, true));
                                $password = $match[1];
                            } else {
                                $test = crypt($this->credential, substr($password, 0, 3));
                            }
                            
                            if ($test === $password) {
                                $result = \org\octris\core\auth::T_AUTH_SUCCESS;
                            } else {
                                $result = \org\octris\core\auth::T_CREDENTIAL_INVALID;
                            }
                        }
                    }
                }

                fclose($fp);
            }

            return new \org\octris\core\auth\identity(
                $result,
                array(
                    'username' => $this->username
                )
            );
        }
    }
}
