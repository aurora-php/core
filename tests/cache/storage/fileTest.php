<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(__DIR__ . '/../storageTest.php');

class fileTest extends storageTest {
    public function setUp() {
        $this->storage = new \octris\core\cache\storage\file(array(
            'ns' => 'octris/core.test'
        ));

        parent::setUp();
    }
}
