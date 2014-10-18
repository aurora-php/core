<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * Shell.
     *
     * @octdoc      c:core/shell
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class shell
    /**/
    {
        /**
         * Standard file descriptors.
         * 
         * @octdoc  m:shell/T_FD_STDIN, T_FD_STDOUT, T_FD_STDERR
         */
        const T_FD_STDIN  = 0;
        const T_FD_STDOUT = 1;
        const T_FD_STDERR = 2;
        /**/

        /**
         * Prepare shell command for execution.
         *
         * @octdoc  m:shell/__callStatic
         * @param   string          $cmd            Command to execute.
         * @param   array           $args           Optional arguments for command.
         * @return  \org\octris\core\shell\command  Instance of shell command class.
         */
        public static function __callStatic($cmd, array $args) {
            $shell_cmd = new \org\octris\core\shell\command(
                $cmd, 
                $args
            );

            return $shell_cmd;
        }
    }

    // test
    $txt = "a\nc\ne\nb\nd\n";
    $fh  = fopen('data://text/plain;base64,' . base64_encode($txt), 'r');

    shell::cat()->
        setPipe(shell::STDIN,  $fh)->
        setPipe(shell::STDOUT, shell::sort())->exec();
}
