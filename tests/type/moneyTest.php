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

class moneyTest extends PHPUnit_Framework_TestCase {
    public function testAllocate() {
        $tests = array(
            array(90, array(70, 30),     array(63  , 27  )),
            array(99, array(70, 30),     array(69.3, 29.7)),
            array( 1, array(30, 30, 30), array(0.34, 0.33, 0.33))
        );

        $mon = new \octris\core\type\money();

        foreach ($tests as $test) {
            list($initial, $ratios, $expected) = $test;

            $results = $mon->set($initial)->allocate($ratios);

            for ($i = 0, $cnt = count($results); $i < $cnt; ++$i) {
                $this->assertEquals($expected[$i], $results[$i]->get());
            }
        }
    }

    public function testExchange() {
        // $exchange = new \octris\core\type\money\exchange\openexchangerates();

        // $mon = new \octris\core\type\money();
        // $mon->setExchangeService($exchange);

        // $mon->set(100, 'EUR')->exchange('GBP');
    }
}
