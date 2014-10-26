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

class stateTest extends PHPUnit_Framework_TestCase {
    protected $state;

    public function setUp() {
        $this->state = new \octris\core\app\state();
    }

    public function testThaw() {
        $state = new \octris\core\app\state();
        $state['test'] = 'test';

        $secret = 'origami';
        $frozen = $state->freeze($secret);

        $thawed = \octris\core\app\state::thaw($frozen, $secret);

        $this->assertEquals($state, $thawed);
    }
}

