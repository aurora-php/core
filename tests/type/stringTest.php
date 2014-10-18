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

class stringTest extends PHPUnit_Framework_TestCase {
    public function testChunk_id() {
        $tests = array(
            array(         1,  9, 3, '0', '/', '000/000/001/'),
            array(     12345,  9, 3, '0', '/', '000/012/345/'),
            array(1234567890,  9, 3, '0', '/', '123/456/789/'),
            array(         1, -9, 3, '0', '/', '100/000/000/'),
        );

        foreach ($tests as $test) {
            list($string, $pad, $chunk_len, $pad_char, $chunk_char, $expected) = $test;

            $this->assertEquals(\octris\core\type\string::chunk_id($string, $pad, $chunk_len, $pad_char, $chunk_char), $expected);
        }
    }
}
