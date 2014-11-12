<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Shell;

/**
 * Shell command.
 *
 * @copyright   copyright (c) 2013-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 *
 * @depends     \Octris\Core\Shell
 */
class Command
{
    /**
     * Command to execute.
     *
     * @type    string
     */
    protected $command;

    /**
     * Command arguments.
     *
     * @type    array
     */
    protected $args;

    /**
     * Current working directory to use for command execution.
     *
     * @type    string
     */
    protected $cwd;

    /**
     * Environment to use when executing  command.
     *
     * @type    array
     */
    protected $env;

    /**
     * Command pipes.
     *
     * @type    array
     */
    protected $pipes = array();

    /**
     * Stream i/o specifications.
     *
     * @type    array
     */
    protected static $stream_specs = array(
        'default'                           => array('pipe', 'w+'),
        \Octris\Core\Shell::FD_STDIN  => array('pipe', 'r'),
        \Octris\Core\Shell::FD_STDOUT => array('pipe', 'w'),
        \Octris\Core\Shell::FD_STDERR => array('pipe', 'w')
    );

    /**
     * Constructor.
     *
     * @param   string          $cmd            Command to execute.
     * @param   array           $args           Optional arguments for command.
     * @param   string          $cwd            Optional current working directory.
     * @param   array           $env            Optional environment to set.
     */
    public function __construct($cmd, array $args = array(), $cwd = null, array $env = array())
    {
        $this->command = escapeshellarg(basename($cmd));
        $this->cwd     = $cwd;
        $this->env     = $env;
        $this->args    = implode(' ', array_map(function ($arg) {
            return escapeshellarg($arg);
        }, $args));
    }

    /**
     * Set defaults for a pipe.
     *
     * @param   int                                 $fd             Fd of pipe to set defaults for.
     */
    protected function setDefaults($fs)
    {
        $this->pipes[$fd] = array(
            'hash'   => null,
            'object' => null,
            'fh'     => null,
            'spec'   => null
        );
    }

    /**
     * Returns file handle of a pipe and changes descriptor specification according to the usage
     * through a file handle.
     *
     * @param   int                                 $fd             Number of file-descriptor to return.
     * @return  resource                                            A Filedescriptor.
     */
    public function usePipeFd($fd)
    {
        if (!isset($this->pipes[$fd])) {
            $this->setDefaults($fd);
        }

        $this->pipes[$fd]['spec'] = (isset(self::$stream_specs[$fd])
                                        ? self::$stream_specs[$fd]
                                        : self::$stream_specs['default']);

        return $fh =& $this->pipes[$fd]['fh'];      /*
                                                     * reference here means:
                                                     * file handle can be changed within the class instance
                                                     * but not outside the class instance
                                                     */
    }

    /**
     * Set pipe of specified type. The second parameter may be one of the following:
     *
     * * resource -- A stream resource
     * * \Octris\Core\Shell\Command -- Another command to connect
     *
     * @param   int                                 $fd             Number of file-descriptor of pipe.
     * @param   mixed                               $io_spec        I/O specification.
     * @return  \Octris\Core\Shell\Command                      Current instance of shell command.
     */
    public function setPipe($fd, $io_spec)
    {
        if ($io_spec instanceof \Octris\Core\Shell\Command) {
            // chain commands
            $this->pipes[$fd] = array(
                'hash'   => spl_object_hash($command),
                'object' => $command,
                'fh'     => $command->usePipeFd(($fd == \Octris\Core\Shell::FD_STDIN
                                                    ? \Octris\Core\Shell::FD_STDOUT
                                                    : \Octris\Core\Shell::FD_STDIN)),
                'spec'   => (isset(self::$stream_specs[$fd])
                                ? self::$stream_specs[$fd]
                                : self::$stream_specs['default'])
            );
        } elseif (is_resource($io_spec)) {
            // assign a stream resource to pipe
            $this->pipes[$fd] = array(
                'hash'   => null,
                'object' => null,
                'fh'     => $io_spec,
                'spec'   => (isset(self::$stream_specs[$fd])
                                ? self::$stream_specs[$fd]
                                : self::$stream_specs['default'])
            );
        }

        return $this;
    }

    /**
     * Execute command.
     */
    public function execute()
    {
        $pipes = array();
        $specs = array_map(function ($p) {
            return $p['spec'];
        }, $this->pipes);

        if (!($proc = proc_open($this->cmd, $specs, $pipes, $this->cwd, $this->env))) {
            throw new \Exception('Unable to run command');
        }

        foreach ($pipes as $i => $r) {
            $this->pipes[$i]['fh'] = $r;
        }
    }
}
