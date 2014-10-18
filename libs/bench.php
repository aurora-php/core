<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * Simple utility class for performing benchmarks.
     *
     * @octdoc      c:core/bench
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class bench
    /**/
    {
        /**
         * Storage for tests.
         *
         * @octdoc  p:bench/$tests
         * @type    array
         */
        protected $tests = array();
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:bench/__construct
         */
        public function __construct()
        /**/
        {
        }

        /**
         * Add test for the benchmark.
         *
         * @octdoc  m:bench/addTest
         * @param   callable                $cb                 Callback to execute for test.
         * @param   string                  $name               Optional name for test.
         */
        public function addTest(callable $cb, $name = null)
        /**/
        {
            $this->tests[]   = array(
                'callback' => $cb,
                'name'     => (is_null($name) ? count($this->tests) + 1 : $name),
                'results'  => array()
            );
        }

        /**
         * Pretty-print test results.
         *
         * @octdoc  m:bench/pprint
         * @param   array                   $results            Test results to print.
         */
        public function pprint(array $results)
        /**/
        {
            $n_max   = 0;
            $v_max   = 0;

            foreach ($results as $name => $value) {
                $n_max = max($n_max, strlen($name));
                $v_max = max($v_max, strlen((int)$value));
            }

            $tpl = sprintf("%%- %ds: %%%0d.6f\n", $n_max, $v_max);

            foreach ($results as $name => $value) {
                printf($tpl, $name, $value);
            }
        }

        /**
         * Run tests.
         *
         * @octdoc  m:bench/run
         * @param   int                     $passes             Number of times the tests should run.
         * @param   int                     $iterations         Additional number of iterations each test callback should be called.
         * @param   bool                    $display            Whether to display test results.
         * @return  array                                       Test results.
         */
        public function run($passes, $iterations, $display = true)
        /**/
        {
            // reset results
            $cnt = count($this->tests);

            for ($t = 0; $t < $cnt; ++$t) {
                $this->tests[$t]['results'] = array();
            }

            // run tests
            for ($p = 0; $p < $passes; ++$p) {
                for ($t = 0; $t < $cnt; ++$t) {
                    $start = microtime(true);

                    for ($i = 0; $i < $iterations; ++$i) {
                        $this->tests[$t]['callback']();
                    }

                    $this->tests[$t]['results'][] = microtime(true) - $start;
                }
            }

            // summary
            $results = array();

            for ($t = 0; $t < $cnt; ++$t) {
                $results[$this->tests[$t]['name']] = array_sum($this->tests[$t]['results']) / $passes;
            }

            if ($display) $this->pprint($results);

            return $results;
        }
    }
}
