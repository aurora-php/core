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

class numberTest extends PHPUnit_Framework_TestCase {
    public function testPrecision() {
        $num = new \octris\core\type\number();

        // 0.1 + 0.2 - 0.3 : -0 (PHP: 5.5511151231258E-17)
        $this->assertEquals($num->set(0.1)->add(0.2)->sub(0.3)->get(), '0.0');
    }

    public function testCeil() {
        $num = new \octris\core\type\number();

        $tests = array('4.3', '9.999', '-3.14');

        foreach ($tests as $test) {
            $this->assertEquals($num->set($test)->ceil()->get(), ceil($test));
        }
    }

    public function testFloor() {
        $num = new \octris\core\type\number();

        $tests = array('4.3', '9.999', '-3.14');

        foreach ($tests as $test) {
            $this->assertEquals($num->set($test)->floor()->get(), floor($test));
        }
    }

    public function testRound() {
        $num = new \octris\core\type\number();

        $tests = array(
            array( '3.4'    , '0',   '3.0' ),
            array( '3.5'    , '0',   '4.0' ),
            array( '3.6'    , '0',   '4.0' ),
            array( '1.95583', '2',   '1.96'),
            array( '5.045'  , '2',   '5.05'),
            array( '5.055'  , '2',   '5.06'),
            array( '9.999'  , '2',  '10.00'),

            array('-3.4'    , '0',  '-3.0' ),
            array('-3.5'    , '0',  '-4.0' ),
            array('-3.6'    , '0',  '-4.0' ),
            array('-1.95583', '2',  '-1.96'),
            array('-5.045'  , '2',  '-5.05'),
            array('-5.055'  , '2',  '-5.06'),
            array('-9.999'  , '2', '-10.00'),
        );

        foreach ($tests as $test) {
            list($value, $precision, $expected) = $test;

            $this->assertEquals($expected, $num->set($value)->round($precision)->get());
        }
    }
}
