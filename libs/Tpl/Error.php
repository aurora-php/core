<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Tpl;

/**
 * Template engine related error output.
 *
 * @copyright   copyright (c) 2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Error
{
    /**
     * Output device. Default output is stdout.
     *
     * @type    resource|null
     */
    protected static $stderr = null;

    /**
     * Set standard error output to specified resource.
     *
     * @param   resource            $stderr                 Resource to output stderr to.
     */
    public static function setStderr($stderr)
    {
        if (!is_resource($stderr)) {
            throw new \Exception('Provided argument is not a resource "' . gettype($stderr) . ':' . $stderr . '"');
        }

        self::$stderr = $stderr;
    }

    /**
     * Write to stderr.
     *
     * @param   string              $context                Context the error occured in.
     * @param   int                 $context_line           Line of the context.
     * @param   array               $info                   Key/Value pairs of information to print.
     * @param   string|null         $trace                  Optional stack trace.
     * @param   \Exception|null     $exception              Optional exception to throw after output.
     */
    public static function write($context, $context_line, array $info, $trace = null, \Exception $exception = null)
    {
        if (is_null(self::$stderr)) {
            self::$stderr = fopen('php://output', 'w');
        }

        // start formatting
        if (($pre = (php_sapi_name() != 'cli' && stream_get_meta_data(self::$stderr)['uri'] == 'php://output'))) {
            fputs(self::$stderr, "<pre>");

            $prepare = function ($str) {
                return htmlentities($str, ENT_QUOTES);
            };
        } else {
            $prepare = function ($str) {
                return $str;
            };
        }

        // general information
        fputs(self::$stderr, sprintf("\n** ERROR: %s(%d)**\n", $context, $context_line));

        $max = array_reduce(array_keys($info), function($carry, $key) {
            return max($carry, strlen($key) + 3);
        }, 0);

        foreach ($info as $key => $value) {
            fputs(self::$stderr, sprintf('   %-' . $max . "s %s\n", $key . ':  ', $prepare($value)));
        }

        // output stacktrace
        if (is_null($trace)) {
            ob_start();
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $trace = ob_get_contents();
            ob_end_clean();
        }

        fputs(self::$stderr, "\n   " . str_replace("\n", "\n   ", trim($trace)) . "\n");

        // end formatting
        if ($pre) {
            fputs(self::$stderr, "</pre>");
        }

        // exception
        if (!is_null($exception)) {
            throw $exception;
        }
    }
}
