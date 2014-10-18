<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\l10n {
    /**
     * CLDR support class.
     *
     * @octdoc      c:l10n/cldr
     * @copyright   copyright (c) 2011-2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class cldr
    /**/
    {
        /**
         * Instance of CLDR class.
         *
         * @octdoc  p:cldr/$instance
         * @type    \octris\core\cldr
         */
        private static $instance = null;
        /**/

        /**
         * Data storage.
         *
         * @octdoc  p:cldr/$storage
         * @type    \octris\core\cache\storage
         */
        private static $storage;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:cldr/__construct
         */
        protected function __construct()
        /**/
        {
        }
        
        /*
         * prevent cloning
         */
        private function __clone() {}

        /**
         * Load CLDR data.
         *
         * @octdoc  m:cldr/getData
         * @param   string                          $name               Name of data file to load.
         * @param   string                          $lc                 Optional locale code.
         * @return  \octris\core\cldr                               CLDR class instance.
         */
        public static function getData($name, $lc = null)
        /**/
        {
            if (is_null($lc)) {
                $lc = \octris\core\l10n::getInstance()->getLocale();
            }

            $data = self::$storage->load('CLDR:' . $name . ':' . $lc, function() use ($name, $lc) {
                $code = explode('_', $lc);
                
                while (count($code) > 0) {
                    $pathname = __DIR__ . '/../../data/cldr/main/' . implode('-', $code) . '/' . $name . '.json';
                    
                    if (!file_exists($pathname)) {
                        array_pop($code);
                        continue;
                    }
                    
                    $json = file_get_contents($pathname);
                    
                    return json_decode($json, true);
                }
            });
            
            return $data;
        }

        /**
         * Load supplemental CLDR data.
         *
         * @octdoc  m:cldr/getData
         * @param   string                          $name               Name of data file to load.
         * @return  \octris\core\cldr                               CLDR class instance.
         */
        public static function getSupplementalData($name)
        /**/
        {
            $data = self::$storage->load('CLDR:' . $name, function() use ($name) {
                $json = file_get_contents(__DIR__ . '/../../data/cldr/supplemental/' . $name . '.json');
                
                return json_decode($json, true);
            });
            
            return $data;
        }

        /**
         * Set cache storage handler for CLDR data.
         *
         * @octdoc  m:cldr/setStorage
         * @param   \octris\core\cache\storage      $storage                Storage handler to set.
         */
        public static function setStorage(\octris\core\cache\storage $storage)
        /**/
        {
            self::$storage = $storage;
        }
    }
    
    cldr::setStorage(new \octris\core\cache\storage\transient());
}