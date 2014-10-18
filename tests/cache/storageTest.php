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

abstract class storageTest extends PHPUnit_Framework_TestCase {
    protected $storage;

    public function setUp() {
        $this->assertInstanceOf(
            'org\octris\core\cache\storage',
            $this->storage,
            'Storage adapter instance is needed for tests'
        );
    }

    /** **/

    public function testLoadAndFetch() {
        $value = uniqid();

        $this->storage->load('key1', function() use ($value) { return $value; });
        $this->assertEquals($value, $this->storage->fetch('key1'));
    }

    public function testSaveAndFetch() {
        $tests = array(
            'scalar'   => uniqid(),
            'array'    => array(1, 2, 3, 4),
            'int'      => 1,
        );

        foreach ($tests as $name => $value) {
            $this->storage->save($name, $value);
            $this->assertEquals($value, $this->storage->fetch($name));
        }
    }

    public function testSaveAndInc() {
        $tests = array(
            array('key1', 0, 1, 1),
            array('key2', 1, 2, 3),
            array('key3', 2, 3, 5)
        );

        foreach ($tests as $test) {
            list($key, $start, $step, $end) = $test;

            $this->storage->save($key, $start);
            $this->storage->inc($key, $step);
            $this->assertEquals($end, $this->storage->fetch($key));
        }
    }

    public function testSaveAndDec() {
        $tests = array(
            array('key1', 0, 1, -1),
            array('key2', 3, 2, 1),
            array('key3', 2, 2, 0)
        );

        foreach ($tests as $test) {
            list($key, $start, $step, $end) = $test;

            $this->storage->save($key, $start);
            $this->storage->dec($key, $step);
            $this->assertEquals($end, $this->storage->fetch($key));
        }
    }

    public function testSaveAndCas() {
        $tests = array(
            array('key1', 2, 1),
            array('key2', 0, 3)
        );

        foreach ($tests as $test) {
            list($key, $start, $end) = $test;

            $this->storage->save($key, $start);
            $this->storage->cas($key, $start, $end);
            $this->assertEquals($end, $this->storage->fetch($key));
        }
    }
}
