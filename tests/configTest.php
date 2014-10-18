<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('octris/core/app/test.class.php');

use \octris\core\app\test as test;

class configTest extends PHPUnit_Framework_TestCase {
    protected function loadConfig() {
        
    }
    
    public function testHasStaticDataProperty() {
        // $class = new ReflectionClass('\octris\core\config');
        // $this->assertTrue($class->hasProperty('data'));
        // 
        // $prop = $class->getProperty('data');
        // $this->assertTrue($prop->isStatic());
    }
}
