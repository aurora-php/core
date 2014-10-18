<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('octris/core/app/test.php');

use \octris\core\app\test as test;

class collectionTest extends PHPUnit_Framework_TestCase {
    function dataProvider() {
        return array(
            new \octris\core\type\collection(array(1, 2, 3)),
            new \octris\core\type\collection(array(
                'eins' => 1, 'zwei', 3, 'vier' => 4
            ))
        );
    }
    
    function assocCollection() {
    }

    function testKeyrename() {
        
    }

    /**
     * @depends testCollection
     */
    function testGetArrayCopy($data) {
        $this->assertEquals($data->getArrayCopy(), array(1, 2, 3));
    }
    
    /**
     * @depends testCollection
     */
    function testCount($data) {
        $this->assertEquals(count($data), 3);
    }
    
    /**
     * @depends testCollection
     */
    function testMerge($data) {
        $data->merge(array(4, 5, 6), array(7, 8, 9));
        
        $this->assertEquals($data->getArrayCopy(), array(1, 2, 3, 4, 5, 6, 7, 8, 9));
    }
}
