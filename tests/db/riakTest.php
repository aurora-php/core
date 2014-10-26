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

class riakTest extends PHPUnit_Framework_TestCase {
    protected $db;
    protected $cn;

    public function setUp() {
        $this->db = new \octris\core\db\device\riak('192.168.178.11', '8098');
        $this->cn = $this->db->getConnection(\octris\core\db::T_DB_MASTER);
    }

    public function testIsAlive() {
        $this->assertTrue($this->cn->isAlive());
    }

    public function testGetCollections() {
        $this->cn->getCollections();
    }

    public function testInsert() {
        $cl = $this->cn->getCollection('test');
        $key = $cl->insert(array('foo' => 'bar'));

        $this->assertTrue(($key !== false));
    }

    public function testFetch() {
        $data = array('foo' => 'bar');

        $cl  = $this->cn->getCollection('test');
        $key = $cl->insert($data);

        $result = $cl->fetch($key);

        $this->assertEquals($data, $result);
    }

    public function testUpdate() {
        $data = array('bar' => 'buzz');

        $cl  = $this->cn->getCollection('test');
        $key = $cl->insert(array('foo' => 'bar'));

        $cl->update($key, $data);

        $result = $cl->fetch($key);

        $this->assertEquals($data, $result);
    }
}
