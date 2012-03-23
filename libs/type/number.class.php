<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\type {
    /**
     * Number type. Uses bcmath functionality for number calculations.
     *
     * @octdoc      c:type/number
     * @copyright   copyright (c) 2010-2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class number extends \org\octris\core\type 
    /**/
    {
        /**
         * Value of object.
         *
         * @octdoc  p:number/$value
         * @var     float
         */
        protected $value = '0';
        /**/
        
        /**
         * Number of digits after the decimal point for a calculated result.
         *
         * @octdoc  p:number/$scale
         * @var     int|null 
         */
        protected $scale = null;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:number/__construct
         * @param   float       $value      Optional value for number.
         * @param   int         $scale      Number of digits after the decimal point for a calculated result.
         */
        public function __construct($value = 0, $scale = null)
        /**/
        {
            $this->value = (string)$value;
            $this->scale = (is_null($scale)
                            ? (($scale = ini_get('precision'))
                                ? $scale
                                : null)
                            : $scale);
        }

        /**
         * Method is called, when number object is casted to a string.
         *
         * @octdoc  m:number/__toString
         * @return  string                      Value of object.
         */
        public function __toString()
        /**/
        {
            return (string)$this->get();
        }

        /**
         * Magic caller to implement calculation functionality.
         *
         * @octdoc  m:number/__call
         * @param   string              $func                                       Name of function to perform.
         * @param   array               $args                                       Arbitrary number of arguments of type float, number or money.
         * @return  \org\octris\core\type\number|\org\octris\core\type\money        Instance of current object.
         */
        public function __call($func, $args)
        /**/
        {
            switch ($func) {
            case 'add':
                array_walk($args, function($v) {
                    $this->value = bcadd($this->value, (string)$v, $this->scale);
                });
                break;
            case 'sub':
                array_walk($args, function($v) {
                    $this->value = bcsub($this->value, (string)$v, $this->scale);
                });
                break;
            case 'mul':
                array_walk($args, function($v) {
                    $this->value = bcmul($this->value, (string)$v, $this->scale);
                });
                break;
            case 'div':
                array_walk($args, function($v) {
                    $this->value = bcdiv($this->value, (string)$v, $this->scale);
                });
                break;
            case 'mod':
                array_walk($args, function($v) {
                    $this->value = bcmod($this->value, (string)$v, $this->scale);
                });
                break;
            }

            return $this;
        }
        
        /**
         * Absolute value.
         *
         * @octdoc  m:number/abs
         * @return  \org\octris\core\type\number|\org\octris\core\type\money        Instance of current object.
         */
        public function abs()
        /**/
        {
            $this->value = ltrim($this->value, '-');

            return $this;
        }

        /**
         * Round fractions up.
         *
         * @octdoc  m:number/ceil
         * @return  \org\octris\core\type\number|\org\octris\core\type\money        Instance of current object.
         */
        public function ceil()
        /**/
        {
            $this->value = (substr($this->value, 0, 1) == '-'
                            ? bcsub($this->value, 0, 0)
                            : bcadd($this->value, 1, 0));

            return $this;
        }

        /**
         * Compare number with another one.
         *
         * @octdoc  m:number/compare
         * @param   mixed               $num    Number to compare with.
         * @return  int                         Returns 0 if the both numbers are equal, 1 if the current number object is larger, -1 if the specified number is larger.
         */
        public function compare($num)
        /**/
        {
            return bccomp($this->value, (string)$num, $this->scale);
        }

        /**
         * Round fractions down.
         *
         * @octdoc  m:number/floor
         * @return  \org\octris\core\type\number|\org\octris\core\type\money        Instance of current object.
         */
        public function floor()
        /**/
        {
            $this->value = (substr($this->value, 0, 1) == '-'
                            ? bcsub($this->value, 1, 0)
                            : bcadd($this->value, 0, 0));

            return $this;
        }

        /**
         * Negate value.
         *
         * @octdoc  m:number/neg
         * @return  \org\octris\core\type\number|\org\octris\core\type\money        Instance of current object.
         */
        public function neg()
        /**/
        {
            $this->value = (substr($this->value, 0, 1) == '-'
                            ? substr($this->value, 1)
                            : '-' . $this->value);

            return $this;
        }

        /**
         * Exponential expression.
         *
         * @octdoc  m:number/pow
         * @exp     mixed               $exp                The exponent.
         * @return  \org\octris\core\type\number|\org\octris\core\type\money        Instance of current object.
         */
        public function pow($exp)
        /**/
        {
            $this->value = bcpow($this->value, (string)$exp, $this->scale);

            return $this;
        }

        /**
         * Rounds the number.
         *
         * @octdoc  m:number/round
         * @param   int                 $precision          Optional number of decimals to round to.
         * @param   int                 $mode               Optional rounding mode.
         * @return  \org\octris\core\type\number|\org\octris\core\type\money        Instance of current object.
         */
        public function round($precision = 0, $mode = PHP_ROUND_HALF_UP)
        /**/
        {
            $this->value = (substr($this->value, 0, 1) == '-' || PHP_ROUND_HALF_UP
                            ? bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision)
                            : bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision));

            return $this;
        }

        /**
         * Calculate the square root.
         *
         * @octdoc  m:number/sqrt
         * @return  \org\octris\core\type\number|\org\octris\core\type\money        Instance of current object.
         */
        public function sqrt()
        /**/
        {
            $this->value = bcsqrt($this->value, $this->scale);

            return $this;
        }

        /**
         * Return value of object.
         *
         * @octdoc  m:number/get
         * @return  float                                   Value.
         */
        public function get()
        /**/
        {
            return (float)(!(bool)(float)$this->value ? ltrim($this->value, '-') : $this->value); // prevents signed zero, which we do not want for formatting reasons.
        }

        /**
         * Set value of object.
         *
         * @octdoc  m:number/set
         * @param   float               $amount             Value to set.
         * @return  \org\octris\core\type\number|\org\octris\core\type\money        Instance of current object.
         */
        public function set($value)
        /**/
        {
            $this->value = $value;

            return $this;
        }
    }
}
