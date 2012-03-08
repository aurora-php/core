<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('org.octris.core/app/test.class.php');

use \org\octris\core\app\test as test;

class sandboxTest extends PHPUnit_Framework_TestCase {
	protected $sandbox;
    
    public function setUp() {
        $this->sandbox = new \org\octris\core\tpl\sandbox();
    }
    
    public function testEachArrayObject() {
    	$data = new ArrayObject(array('a', 'b', 'c'));
    	$item = null;
    	$meta = array();

    	for ($i = 0, $cnt = count($data), $max = ($cnt + 1) * 2; $i < $max; ++$i) {
	    	$return = $this->sandbox->each('each1', $ctrl, $data, $meta);

	    	if (($test = $i % ($cnt + 1)) == 3) {
	    		if ($return !== false) {
	    			$this->fail(sprintf("expected \$return to be false at $test"));
	    		}
	    	} else {
	    		switch ($test) {
	    		case 0:
	    			$this->assertEquals('a', $ctrl);
	    			$this->assertEquals(
	    				array('key' => 0, 'pos' => 0, 'count' => 3, 'is_first' => true, 'is_last' => false),
	    				$meta, 
	    				"iteration $i/$test"
	    			);
	    			break;
	    		case 1:
	    			$this->assertEquals('b', $ctrl);
	    			$this->assertEquals(
	    				array('key' => 1, 'pos' => 1, 'count' => 3, 'is_first' => false, 'is_last' => false),
	    				$meta, 
	    				"iteration $i/$test"
	    			);
	    			break;
	    		case 2:
	    			$this->assertEquals('c', $ctrl);
	    			$this->assertEquals(
	    				array('key' => 2, 'pos' => 2, 'count' => 3, 'is_first' => false, 'is_last' => true),
	    				$meta, 
	    				"iteration $i/$test"
	    			);
	    			break;
	    		default:
	    			$this->fail(sprintf("unexpected iteration at $test"));
	    			break;
	    		}
	    	}
    	}
    }
}
