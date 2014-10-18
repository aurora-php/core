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

class uriTest extends PHPUnit_Framework_TestCase {
    public function testUrlExampleOrg() {
        $url = new \octris\core\type\uri('http://www.example.org/');

        $this->assertEquals('http', $url->scheme);
        $this->assertEquals('www.example.org', $url->host);
        $this->assertEquals('/', $url->path);
    }

    public function testUrlGetRequest() {
        $url = new \octris\core\type\uri('https://www.example.com/?key1=val1&key2=val2');

        $this->assertEquals('https', $url->scheme);
        $this->assertEquals('www.example.com', $url->host);
        $this->assertEquals('/', $url->path);

        $this->assertTrue(isset($url->query['key1']));
        $this->assertEquals('val1', $url->query['key1']);

        $this->assertTrue(isset($url->query['key2']));
        $this->assertEquals('val2', $url->query['key2']);

        $this->assertFalse(isset($url->query['key3']));
    }

    public function testUrlGetRequestModify() {
        $url = new \octris\core\type\uri('https://www.example.com/?key1=val1&key2=val2');

        unset($url->query['key2']);
        $url->query['key3'] = 'val3';

        $this->assertEquals('https://www.example.com/?key1=val1&key3=val3', (string)$url);
    }
}
