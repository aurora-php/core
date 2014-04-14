<?php

/*
 * This file is part of the '{{$directory}}' package.
 *
 * (c) {{$company}}
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{$namespace}}\app {
    /**
     * Main application class. This class is only used to define an entry page - if it's the
     * first request to the web application and therefore no other page (next_page) is specified through the 
     * application state, this entry page is required.
     *
     * @octdoc      c:app/main
     * @copyright   copyright (c) {{$year}} by {{$company}}
     * @author      {{$author}} <{{$email}}>
     */
    class main extends \org\octris\core\app\web
    /**/
    {
        /**
         * Page to use a entry point, if no "next_page" is specified through the
         * application state.
         *
         * @octdoc  p:main/$entry_page
         * @type    string
         */
        protected $entry_page = '{{$namespace}}\app\entry';
        /**/
    }
}
