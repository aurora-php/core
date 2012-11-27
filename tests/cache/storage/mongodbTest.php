<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(__DIR__ . '/../storageTest.php');

class mongodbTest extends storageTest {
    public function setUp() {
        $this->storage = new \org\octris\core\cache\storage\mongodb(array(
            'ns' => 'org.octris.core.test'
        ));

        parent::setUp();
    }
}
