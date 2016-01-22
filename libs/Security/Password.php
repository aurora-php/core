<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Security;

if (!defined('PASSWORD_PBKDF2')) {
    define('PASSWORD_PBKDF2', 'pbkdf2');
}

/**
 * Password hashing and validation.
 *
 * @copyright   copyright (c) 2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Password
{
    /**
     * Hashing algorithm for PBKDF2.
     *
     * @type    string
     */
    const PBKDF2_ALGO = 'sha1';

    /**
     * Default iterations.
     *
     * @type    int
     */
    const PBKDF2_ITERATIONS = 64000;

    /**
     * Test if hash is a valid PBKDF2 hash.
     *
     * @param   string          $hash                   Password hash.
     * @return  bool
     */
    protected static function isPbkdf2($hash)
    {
        return (preg_match('/^\$p5k2\$[0-9a-fA-F]+(\$[0-9a-zA-Z+\/]*={0,2}+){2}$/', $hash));
    }

    /**
     * Return a list of supported password algorithms.
     *
     * @return  array                                   List of password algorithms.
     */
    public static function getAlgorithms()
    {
        $algos = array_filter(
            get_defined_constants(),
            function($key) {
                return (preg_match('/^PASSWORD_[^_]+$/', $key) && $key != 'PASSWORD_DEFAULT');
            },
            ARRAY_FILTER_USE_KEY
        );

        $return = array_map(
            function($k, $v) {
                return [
                    'algo' => $v,
                    'name' => strtolower(explode('_', $k)[1]),
                    'constant' => $k,
                    'is_default' => ($v === PASSWORD_DEFAULT)
                ];
            },
            array_keys($algos),
            $algos
        );

        return $return;
    }

    /**
     * Return information about a password hash.
     *
     * @param   string          $hash                   Password hash.
     * @return  array                                   Information about the password hash.
     */
    public static function getInfo($hash)
    {
        if (self::isPbkdf2($hash)) {
            $return = [
                'algo' => PASSWORD_PBKDF2,
                'algoName' => 'pbkdf2',
                'options' => [
                    'iterations' => hexdec(explode('$', substr($hash, 1))[1])
                ]
            ];
        } else {
            $return = password_get_info($hash);
        }

        return $return;
    }

    /**
     * Create hash for password.
     *
     * @param   string          $password               Password to hash.
     * @param   int|string      $algo                   Algorithm to use for hashing.
     * @param   array           $options                Optional options.
     * @return  string                                  Password hash.
     */
    public static function hash($password, $algo, array $options = array())
    {
        switch ($algo) {
            case PASSWORD_PBKDF2:
                $options = array_merge(['iterations' => self::PBKDF2_ITERATIONS], $options);

                $salt = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
                $hash = hash_pbkdf2(self::PBKDF2_ALGO, $password, $salt, $options['iterations'], 0);

                $return = '$p5k2$' . implode('$', [
                    dechex($options['iterations']),
                    base64_encode($salt),
                    base64_encode($hash)
                ]);

                break;
            default:
                $return = password_hash($password, $algo, $options);
                break;
        }

        return $return;
    }

    /**
     * Verify password and hash.
     *
     * @param   string          $password               Password to verify.
     * @param   string          $hash                   Password hash to compare to.
     * @return  bool                                    Returns true if verification succeeded.
     */
    public static function verify($password, $hash)
    {
        if (self::isPbkdf2($hash)) {
            list(, $iterations, $salt, $hash) = explode('$', substr($hash, 1));

            $return = (hash_pbkdf2(self::PBKDF2_ALGO, $password, base64_decode($salt), hexdec($iterations), 0) === base64_decode($hash));
        } else {
            $return = password_verify($password, $hash);
        }

        return $return;
    }

    /**
     * Test if hash needs rehashing.
     *
     * @param   string          $password               Password to hash.
     * @param   int|string      $algo                   Algorithm to use for hashing.
     * @param   array           $options                Optional options.
     * @return  bool                                    Password hash.
     */
    public static function needsRehash($hash, $algo, array $options = array())
    {
        $info = self::getInfo($hash);

        if ($info['algo'] !== $algo) {
            $return = true;
        } elseif ($info['algo'] == PASSWORD_PBKDF2) {
            $options = array_merge(['iterations' => self::PBKDF2_ITERATIONS], $options);

            $return = ($info['options']['iterations'] !== $options['iterations']);
        } else {
            $return = password_needs_rehash($hash, $algo, $options);
        }
    }
}
