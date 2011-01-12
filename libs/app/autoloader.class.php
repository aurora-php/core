<?php

namespace org\octris\core\app {
    /**
     * Class Autoloader.
     *
     * @octdoc      c:app/autoloader
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class autoloader
    /**/
    {
        /**
         * Class Autoloader.
         *
         * @octdoc  m:app/autoload
         * @param   string      $classpath      Path of class to load.
         */
        public static function autoload($classpath)
        /**/
        {
            $pkg = preg_replace('|\\\\|', '/', preg_replace('|\\\\|', '.', ltrim($classpath, '\\\\'), 2)) . '.class.php';

            require_once($pkg);
        }
    }

    spl_autoload_register(array('\org\octris\core\app\autoloader', 'autoload'));
}