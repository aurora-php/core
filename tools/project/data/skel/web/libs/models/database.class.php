<?php

/*
 * This file is part of the '{{$directory}}' package.
 *
 * (c) {{$company}}
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{$namespace}}\models {
    /**
     * Proxy for accessing different database backends.
     *
     * @octdoc      c:models/database
     * @copyright   copyright (c) {{$year}} by {{$company}}
     * @author      {{$author}} <{{$email}}>
     */
    class database
    /**/
    {
        /**
         * Instance of database backend class.
         *
         * @octdoc  p:database/$backend
         * @type    \{{$namespace}}\models\database
         */
        protected $backend;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:database/__construct
         * @param   array           $settings                   Database connection settings.
         */
        public function __construct(array $settings)
        /**/
        {
            $class = get_class($this);
            $class = substr($class, strrpos($class, '\\'));
            $class = '{{$namespace}}\models\\' . $settings['device'] . $class;
            
            $this->backend = new $class($settings);
        }
    }
}
