<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core;

use \Octris\Core\Tpl\Compiler as compiler;

/**
 * Main class of template engine.
 *
 * @copyright   copyright (c) 2010-2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Tpl
{
    /**
     * Escape types.
     *
     */
    const T_ESC_NONE = '';
    const T_ESC_AUTO = 'auto';
    const T_ESC_ATTR = 'attr';
    const T_ESC_CSS  = 'css';
    const T_ESC_HTML = 'html';
    const T_ESC_JS   = 'js';
    const T_ESC_TAG  = 'tag';
    const T_ESC_URI  = 'uri';
    
    /**
     * Instance of sandbox for executing template in.
     *
     * @type    \octris\core\tpl\sandbox
     */
    protected $sandbox;
    
    /**
     * Whether to fetch compiled template from cache.
     *
     * @type    bool
     */
    protected $use_cache = false;
    
    /**
     * Stores pathes to look into when searching for template to load.
     *
     * @type    array
     */
    protected $searchpath = array();
    
    /**
     * Instance of locale class.
     *
     * @type    \octris\core\l10n
     */
    protected $l10n;
    
    /**
     * Output path for various file types.
     *
     * @type    array
     */
    protected $path = array(
        'tpl'   => '/tmp',      // output path for compiled templates
        'js'    => '/tmp',      // output path for compressed javascript
        'css'   => '/tmp'       // output path for compressed css
    );
    
    /**
     * Resource pathes for various file types.
     *
     * @type    array
     */
    protected $resources = array(
        'tpl'   => '',
        'js'    => '',
        'css'   => ''
    );
    
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $this->sandbox = new Tpl\Sandbox();
    }

    /**
     * Set l10n dependency.
     *
     * @param   \Octris\Core\L10n       $l10n       Instance of l10n class.
     */
    public function setL10n(\Octris\Core\L10n $l10n)
    {
        $this->sandbox->setL10n($l10n);
        $this->l10n = $l10n;
    }

    /**
     * Set values for multiple template variables.
     *
     * @param   array       $array      Key/value array with values.
     */
    public function setValues($array)
    {
        $this->sandbox->setValues($array);
    }

    /**
     * Set value for one template variable.
     *
     * @param   string      $name       Name of template variable to set value of.
     * @param   mixed       $value      Value to set for template variable.
     */
    public function setValue($name, $value)
    {
        $this->sandbox->setValue($name, $value);
    }

    /**
     * Register a custom template method.
     *
     * @param   string      $name       Name of template method to register.
     * @param   mixed       $callback   Callback to map to template method.
     * @param   array       $args       Optional parametert for specifying min/max number of arguments required for callback method.
     */
    public function registerMethod($name, $callback, array $args = array('min' => 0, 'max' => 0))
    {
        $this->sandbox->registerMethod($name, $callback, $args);
    }

    /**
     * Register pathname for looking up templates in.
     *
     * @param   mixed       $pathname       Name of path to register.
     */
    public function addSearchPath($pathname)
    {
        if (is_array($pathname)) {
            foreach ($pathname as $path) $this->addSearchPath($path);
        } else {
            if (!in_array($pathname, $this->searchpath)) {
                $this->searchpath[] = $pathname;
            }
        }
    }

    /**
     * Set path for a resource like stylesheets, images according to the
     * specified extension.
     *
     * @param   string      $ext        Extension of file to set path for.
     * @param   string      $pathname   Name of path to register.
     */
    public function setResourcePath($ext, $pathname)
    {
        if (array_key_exists($ext, $this->resources) && is_dir($pathname)) {
            $this->resources[$ext] = rtrim($pathname, '/');
        }
    }

    /**
     * Set output path for compiled templates and compressed files.
     *
     * @param   string      $ext        Extension of file to set path for.
     * @param   string      $pathname   Name of path to register.
     */
    public function setOutputPath($ext, $pathname)
    {
        if (array_key_exists($ext, $this->path) && is_writable($pathname)) {
            $this->path[$ext] = rtrim($pathname, '/');
        }
    }

    /**
     * Set cache for template snippets.
     *
     * @param   \Octris\Core\Cache      $cache          Caching instance.
     */
    public function setSnippetCache(\Octris\Core\Cache $cache)
    {
        $this->sandbox->setSnippetCache($cache);
    }

    /**
     * Executes template toolchain -- compiler and compressors.
     *
     * @param   string      $inp        Input filename.
     * @param   string      $out        Output filename.
     * @param   string      $escape     Escaping to use.
     */
    protected function process($inp, $out, $escape)
    {
        // Tpl\Compiler\Constant::setConstants($this->constants);
        $sandbox = $this->sandbox;

        $c = new Tpl\Compiler();
        if (!is_null($this->l10n)) $c->setL10n($this->l10n);
        $c->addSearchPath($this->searchpath);

        if (($filename = $c->findFile($inp)) !== false) {
            $tpl = $c->process($filename, $escape);

            $tpl = Tpl\Compress::process($tpl, $this->path, $this->resources);
            $out = $this->path['tpl'] . '/' . str_replace('/', '-', $out);

            file_put_contents($out, $tpl);
        } else {
            die(sprintf(
                'unable to locate file "%s" in "%s"',
                $inp,
                implode(':', $this->searchpath)
            ));
        }

        return $out;
    }

    /**
     * Compile template and return compiled template as string.
     *
     * @param   string      $filename       Name of template file to compile.
     * @param   string      $escape         Optional escaping to use.
     * @return  string                      Compiled template.
     */
    public function compile($filename, $escape = self::T_ESC_HTML)
    {
        $inp = ltrim(preg_replace('/\/\/+/', '/', preg_replace('/\.\.?\//', '/', $filename)), '/');
        $tpl = '';

        $sandbox = $this->sandbox;

        $c = new Tpl\Compiler();
        $c->setL10n($this->l10n);
        $c->addSearchPath($this->searchpath);

        if (($filename = $c->findFile($inp)) !== false) {
            $tpl = $c->process($filename, $escape);
        } else {
            die(sprintf(
                'unable to locate file "%s" in "%s"',
                $inp,
                implode(':', $this->searchpath)
            ));
        }

        return $tpl;
    }

    /**
     * Render a template and send output to stdout.
     *
     * @param   string      $filename       Filename of template to render.
     * @param   string      $escape         Optional escaping to use.
     */
    public function render($filename, $escape = self::T_ESC_HTML)
    {
        $inp = ltrim(preg_replace('/\/\/+/', '/', preg_replace('/\.\.?\//', '/', $filename)), '/');
        $out = preg_replace('/[\s\.]/', '_', $inp) . '.php';

        if (!$this->use_cache) {
            // do not use cache -- first process template using
            // template compiler and javascript/css compressor
            $out = $this->process($inp, $out, $escape);
        }

        $this->sandbox->render($out);
    }

    /**
     * Render a template and return output as string.
     *
     * @param   string      $filename       Filename of template to render.
     * @param   string      $escape         Optional escaping to use.
     * @return  string                      Rendered template.
     */
    public function fetch($filename, $escape = self::T_ESC_HTML)
    {
        ob_start();

        $this->render($filename, $escape);

        $return = ob_get_contents();
        ob_end_clean();

        return $return;
    }

    /**
     * Render a template and save output to a file.
     *
     * @param   string      $savename       Filename to save output to.
     * @param   string      $filename       Filename of template to render.
     * @param   string      $escape         Optional escaping to use.
     */
    public function save($savename, $filename, $escape = self::T_ESC_HTML)
    {
        file_put_contents($savename, $this->fetch($filename, $escape));
    }
}