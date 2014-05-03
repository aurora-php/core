<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\project\app {
    use \org\octris\core\app as app;
    use \org\octris\core\config as config;
    use \org\octris\core\app\cli\stdio as stdio;
    use \org\octris\core\validate as validate;

    /**
     * Create new project.
     *
     * @octdoc      c:app/create
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class create extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Application data.
         *
         * @octdoc  p:create/$data
         * @type    array
         */
        protected $data = array();
        /**/

        /**
         * Type of application to create.
         *
         * @octdoc  p:create/$type
         * @type    string
         */
        protected $type;
        /**/

        /**
         * Application types.
         *
         * @octdoc  p:create/$types
         * @type    array
         */
        protected static $types = array(
            'w' => 'web',
            'c' => 'cli',
            'l' => 'lib'
        );
        /**/

        /**
         * Helper method to test whether a file is binary or text file.
         *
         * @octdoc  m:create/isBinary
         * @param   string          $file               File to test.
         * @param   string          $size               Optional block size to test.
         * @return  bool                                Returns true for binaries.
         */
        protected function isBinary($file, $size = 2048)
        /**/
        {
            $return = false;

            if (is_file($file) && is_readable($file) && ($fp = fopen($file, 'r'))) {
                $blk = fread($fp, $size);
                fclose($fp);

                $return = (substr_count($blk, "\x00") > 0);
            }

            return $return;
        }

        /**
         * Prepare page
         *
         * @octdoc  m:create/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
            // import project name
            $args = \org\octris\core\provider::access('args');

            if ($args->isExist('p') && $args->isValid('p', \org\octris\core\validate::T_PROJECT)) {
                $project = $args->getValue('p');

                $tmp    = explode('.', $project);
                $module = array_pop($tmp);
                $domain = implode('.', array_reverse($tmp));
            } else {
                $module = '';
                $domain = '';
            }

            // handle project configuration
            $prj = new config('org.octris.core', 'project.create');

            $prj->setDefaults(array(
                'info.company' => (isset($data['company']) ? $data['company'] : ''),
                'info.author'  => (isset($data['author']) ? $data['author'] : ''),
                'info.email'   => (isset($data['email']) ? $data['email'] : '')
            ));

            if ($domain != '') {
                $prj['info.domain'] = $domain;
            }

            // collect information and create configuration for new project
            $filter = $prj->filter('info');

            foreach ($filter as $k => $v) {
                $prj[$k] = stdio::getPrompt(sprintf("%s [%%s]: ", $k), $v);
            }

            $prj->save();

            print "\n";

            $types = array();
            array_walk(self::$types, function(&$v, $k) use (&$types) {
                $types[] = preg_replace('/(' . $k . ')/', '(\1)', $v, 1);
            });

            do {
                $this->type = stdio::getPrompt('application type ' . implode(' / ', $types) . ': ', '', true);
            } while (!in_array($this->type, array_keys(self::$types)));
                
            print "\n";

            $module = stdio::getPrompt('module [%s]: ', $module, true);
            $year   = stdio::getPrompt('year [%s]: ', date('Y'), true);

            if ($module == '' || $year == '') {
                die("'module' and 'year' are required!\n");
            }

            // build data array
            $ns = implode(
                '\\',
                array_reverse(
                    explode('.', $prj['info.domain'])
                )
            ) . '\\' . $module;

            $project = str_replace('\\', '.', $ns);

            $this->data = array_merge($prj->filter('info')->getArrayCopy(true), array(
                'year'      => $year,
                'module'    => $module,
                'namespace' => $ns,
                'directory' => $project,
                'project'   => $project
            ));
        }

        /**
         * Validate parameters.
         *
         * @octdoc  m:create/validate
         * @param   string                          $action         Action that led to current page.
         * @return
         */
        public function validate()
        /**/
        {
            return true;
        }

        /**
         * Implements dialog.
         *
         * @octdoc  m:create/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }

        /**
         * Render.
         *
         * @octdoc  m:create/render
         */
        public function render()
        /**/
        {
            if (!($dir = \org\octris\core\app\cli::getPath(\org\octris\core\app\cli::T_PATH_WORK, ''))) {
                die("unable to resolve work directory\n");
            }

            if (!isset(self::$types[$this->type])) {
                die(sprintf("unknown application type '%s'\n", $this->type));
            }

            $src = __DIR__ . '/../data/skel/' . self::$types[$this->type] . '/';
            if (!is_dir($src)) {
                die(sprintf("unable to locate template directory '%s'\n", $src));
            }

            $dir  = substr($dir, 0, strrpos($dir, '/')) . '/' . $this->data['directory'];
            if (is_dir($dir)) {
                die(sprintf("there seems to be already a project at '%s'\n", $dir));
            }

            // process skeleton and write project files
            $tpl = new \org\octris\core\tpl();
            $tpl->addSearchPath($src);
            $tpl->setValues($this->data);

            \org\octris\core\tpl\compiler\constant::setConstant(
                'OCTRIS_BASE', 
                \org\octris\core\app\cli::getPath(\org\octris\core\app\cli::T_PATH_BASE, '')
            );

            $len = strlen($src);

            mkdir($dir, 0755);

            $directories = array();
            $iterator    = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $filename => $cur) {
                $rel   = substr($filename, $len);
                $dst   = $dir . '/' . $rel;
                $path  = dirname($dst);
                $base  = basename($filename);
                $ext   = preg_replace('/^\.?[^\.]+?(\..+|)$/', '\1', $base);
                $base  = basename($filename, $ext);
                $perms = $cur->getPerms();

                if (substr($base, 0, 1) == '$' && isset($this->data[$base = ltrim($base, '$')])) {
                    // resolve variable in filename
                    $dst = $path . '/' . $this->data[$base] . $ext;
                }

                if (!is_dir($path)) {
                    // create destination directory
                    mkdir($path, $cur->getPathInfo()->getPerms(), true);
                }

                if (!$this->isBinary($filename)) {
                    $cmp = $tpl->fetch($rel, \org\octris\core\tpl::T_ESC_NONE);

                    file_put_contents($dst, $cmp);
                } else {
                    copy($filename, $dst);
                }

                chmod($dst, $perms);
            }

            print "done.\n";
        }
    }
}
