#!/usr/bin/env php
<?php

namespace org\octris\core\project {
    /**
     * Tool for creating a new web application project based on a skeleton.
     *
     * @octdoc      h:tools/project
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    /**/

    $_ENV['OCTRIS_APP'] = 'org.octris.core';

    // include core cli application library
    require_once('org.octris.core/app/cli.class.php');
    
    // load application configuration
    $registry = \org\octris\core\registry::getInstance();
    $registry->set('config', function() {
        return new \org\octris\core\config('org.octris.core');
    }, \org\octris\core\registry::T_SHARED | \org\octris\core\registry::T_READONLY);

    // run application
    app\main::getInstance()->invoke(new app\create());
}
