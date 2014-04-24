#!/usr/bin/env php
<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Test a project using PHP's built-in webserver. It's not recommended to use
 * this tool in production environment.
 *
 * @octdoc      h:project/check
 * @copyright   copyright (c) 2012-2013 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
/**/

if (!isset($_ENV['OCTRIS_BASE'])) {
    die("OCTRIS_BASE is not set\n");
}


if (php_sapi_name() == 'cli') {
    // initialization
    $version   = '5.4.0';
    $bind_ip   = '127.0.0.1';
    $bind_port = '8888';
    $router    = null;
    $pid_file  = null;
    $env       = array();

    // php version check
    if (version_compare(PHP_VERSION, $version) < 0) {
        die(sprintf(
            "Unable to start webserver. Please upgrade to PHP version >= '%s'. Your version is '%s'\n",
            $version,
            PHP_VERSION
        ));
    }

    // process command-line arguments
    $options = getopt(
        'b:p:h',
        array('bind-ip:', 'port:', 'project:', 'pid-file:', 'help', 'env:', 'doc-root:')
    );
    
    if (isset($options['h']) || isset($options['help'])) {
        printf("Usage: %s [OPTIONS] --project ...\n", $argv[0]);
        print "  -b, --bind-ip    A single IP that the webserver will be listening on.\n";
        printf("                   (defaults to %s)\n", $bind_ip);
        print "  -p, --port       A port number the webserver will be listening on.\n";
        printf("                   (defaults to %d)\n", $bind_port);
        print "  --doc-root       Alternative document-root directory\n";
        print "  --env            Additional environment variable(s) to set. This option\n";
        print "                   can be specified multiple times and the option value has\n";
        print "                   to be in the form 'name=value'.\n";
        print "  --project        The name of a project to use with this webserver instance.\n";
        print "  --pid-file       A file to write the pid to. the file will be overwritten.\n";
        print "                   (default does not write a PID file)\n";

        die(0);
    }

    if (isset($options['b'])) {
        $bind_ip = $options['b'];
    } elseif (isset($options['bind-ip'])) {
        $bind_ip = $options['bind-ip'];
    }
    if (isset($options['p'])) {
        $bind_port = $options['p'];
    } elseif (isset($options['port'])) {
        $bind_port = $options['port'];
    }
    if (isset($options['project'])) {
        $project = $options['project'];
        $docroot = $_ENV['OCTRIS_BASE'] . '/host/' . $project;

        if (!($trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))) {
            // called directly
            $router  = __FILE__;
        } else {
            // included in some other PHP script
            $tmp    = array_pop($trace);
            $router = $tmp['file'];
        }
        
        if (!is_dir($docroot)) {
            printf(
                "Unknown project or project not installed '%s'.\n",
                $project
            );
            die(255);
        }
        if (!is_file($router)) {
            printf(
                "Request routing script not found '%s'.\n",
                $router
            );
            die(255);
        }
    } else {
        printf("Usage: %s [OPTIONS] --project ...\n", $argv[0]);
        die(255);
    }

    if (isset($options['doc-root'])) {
        $docroot = $options['doc-root'];
        
        if (!is_dir($docroot)) {
            printf(
                "Document-root is no directory '%s'.\n",
                $docroot
            );
            die(255);
        }
    }

    if (isset($options['pid-file'])) {
        $pid_file = $options['pid-file'];
    }

    if (!is_null($pid_file) && !touch($pid_file)) {
        printf(
            "Unable to create PID file or PID file is not writable '%s'.\n",
            $pid_file
        );
        die(255);
    }

    if (isset($options['env'])) {
        $tmp = (is_array($options['env'])
                ? $options['env']
                : (array)$options['env']);

        foreach ($tmp as $_tmp) {
            if (!preg_match('/^([a-z_]+[a-z0-9_]*)=(.*)$/i', $_tmp, $match)) {
                printf(
                    "WARNING: skipping invalid environment variable '%s'.\n",
                    $_tmp
                );
            } else {
                $env[] = $match[1] . '=' . escapeshellarg($match[2]);
            }
        }
    }

    // start php's builtin webserver
    $pid = exec(sprintf(
        '((OCTRIS_APP=%s %s %s -d output_buffering=on -t %s -S %s:%s %s 1>/dev/null 2>&1 & echo $!) &)',
        $project,
        implode(' ', $env),
        PHP_BINARY,
        $docroot,
        $bind_ip,
        $bind_port,
        $router
    ));
    sleep(1);

    if (ctype_digit($pid) && posix_kill($pid, 0)) {
        printf(
            "%s listening on '%s:%s', PID is %d\n",
            basename($argv[0]),
            $bind_ip,
            $bind_port,
            $pid
        );
        die(0);
    } else {
        printf(
            "Unable to start %s on '%s:%s'\n",
            basename($argv[0]),
            $bind_ip,
            $bind_port
        );
        die(255);
    }

    if (!is_null($pid_file)) {
        file_put_contents($pid_file, $pid);
    }

    exit(0);
} elseif (php_sapi_name() != 'cli-server') {
    printf(
        "unable to execute '%s' in environment '%s'\n",
        basename($argv[0]),
        php_sapi_name()
    );
    die(255);
}

// remove possible shebang from output (started using '-d output_buffering=on' [see above])
ob_end_clean();

$ext = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION);

if ($ext == '' || $ext == 'php') {
    $docroot = $_ENV['OCTRIS_BASE'] . '/host/' . $_ENV['OCTRIS_APP'];

    require_once($docroot . '/index.php');
} else {
    return false;
}
