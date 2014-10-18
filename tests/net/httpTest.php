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

class httpTest extends PHPUnit_Framework_TestCase {
    public function testFetch() {
        $curl = new \octris\core\net\client\http(new \octris\core\type\uri('http://www.octris.org/ok.txt'));
        $result = $curl->execute();

        $this->assertEquals('ok', trim($result));
    }

    public function testMultiFetch() {
        $max = 10;
        $cnt = 0;
        
        $net = new \octris\core\net();
        $net->setConcurrency(3);

        for ($i = 0; $i < $max; ++$i) {
            $client = new \octris\core\net\client\http(new \octris\core\type\uri('http://www.octris.org/ok.php?id=' . ($i + 1)));
            $client->setListener(function($result) use (&$cnt) {
                ++$cnt;

                $this->assertEquals('ok:' . $cnt, trim($result));                
            });
            
            $net->addClient($client);
        }

        $net->execute();

        $this->assertEquals($max, $cnt);
    }
}
