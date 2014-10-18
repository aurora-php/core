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
     * Money type.
     *
     * @octdoc      c:type/money
     * @copyright   copyright (c) 2010-2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class money extends \org\octris\core\type\number
    /**/
    {
        /**
         * Currency of money object (ISO 4217)
         *
         * @octdoc  p:money/$currency
         * @type    string
         */
        protected $currency = 'EUR';
        /**/

        /**
         * Stores instance of money exchange class.
         *
         * @octdoc  p:money/$xchg_service
         * @type    \org\octris\core\type\money\exchange_if
         */
        protected static $xchg_service = null;
        /**/

        /**
         * Stores money precision.
         *
         * @octdoc  p:money/$precision
         * @type    int
         */
        protected $precision;
        /**/

        /**
         * Constructor. Note that a money object can have a currency, which is not bound to the 
         * currently set locale. If a precision is specifed, the precision will only be used for 
         * returning the money amount. For internal calculations the default precision will be
         * used.
         *
         * @octdoc  m:money/__construct
         * @param   float       $value      Optional value for money object without locale specific characters.
         * @param   string      $currency   Optional curreny (ISO 4217) to set.
         * @param   int         $precision  Optional precision to use.
         */
        public function __construct($value = 0, $currency = null, $precision = 2)
        /**/
        {
            if (!is_null($currency)) {
                $this->currency = $currency;
            }

            $this->precision = $precision;

            parent::__construct($value);
        }

        /**
         * Magic setter.
         *
         * @octdoc  m:money/__set
         * @param   string          $name           Name of property to set.
         * @param   mixed           $value          Value to set for property.
         */
        public function __set($name, $value)
        /**/
        {
            switch ($name) {
            case 'value':
                $this->value = (string)$value;
                break;
            case 'currency':
                throw new \Exception('The currency is read-only');
                break;
            }
        }

        /**
         * Magic getter.
         *
         * @octdoc  m:money/__get
         * @param   string          $name           Name of property to get.
         */
        public function __get($name)
        /**/
        {
            switch ($name) {
            case 'value':
                $return = $this->get();
                break;
            case 'currency':
                $return = $this->currency;
                break;
            }

            return $return;
        }

        /**
         * Set an object instance, that handles money exchange between currencies.
         *
         * @octdoc  m:money/setExchangeService
         * @param   \org\octris\core\type\money\exchange_if     $service    Instance of a money exchange service.
         */
        public static function setExchangeService(\org\octris\core\type\money\exchange_if $service)
        /**/
        {
            self::$xchg_service = $service;
        }

        /**
         * Allocate the amount of money between multiple targets.
         *
         * @octdoc  m:money/allocate
         * @param   array               $ratios         Ratios to allocate.
         * @return  array                               Array of objects of type \org\octris\core\type\money.
         */
        public function allocate(array $ratios)
        /**/
        {
            $total  = (new \org\octris\core\type\number())->add($ratios);
            $remain = new \org\octris\core\type\number($this->value);
            $return = array();

            for ($i = 0, $cnt = count($ratios); $i < $cnt; ++$i) {
                $return[$i] = clone $this;
                $return[$i]->mul($ratios[$i])->div($total)->round($this->precision);

                $remain->sub($return[$i]);
            }

            $unit = (new \org\octris\core\type\number(10))->pow(-$this->precision);
            $i    = 0;

            while ($remain->get() > 0) {
                $return[($i % $cnt)]->add($unit);
                $remain->sub($unit);

                ++$i;
            }

            return $return;
        }

        /**
         * Compare money with another one and return true, if both money objects are equal. Comparing includes currency. Only money
         * objects with the same currency can ever by equal.
         *
         * @octdoc  m:money/equals
         * @param   mixed               $num    Number to compare with.
         * @return  bool                        Returns true, if money objects are equal.
         */
        public function equals($num)
        /**/
        {
            if (($return = (is_object($num) && $num instanceof \org\octris\core\type\money))) {
                $return = ($this->currency === $num->currency && parent::equals($num));
            }

            return $return;
        }

        /**
         * Convert money object to an other currency using specified exchange rate.
         *
         * @octdoc  m:money/exchange
         * @param   string      $currency           Currency to convert to.
         * @param   float       $rate               Optional exchange rate. The exchange rate -- if specified -- will
         *                                          prevent the call of any set exchange service callback.
         * @param   string      $old_currency       Optional parameter which get's filled from the method with the original currency of the money object.
         * @return  \org\octris\core\type\money     Instance of current money object.
         */
        public function exchange($currency, $rate = null, &$old_currency = null)
        /**/
        {
            if (is_null($rate)) {
                if (is_null(self::$xchg_service)) {
                    throw new \Exception('No money exchange service has been configured');
                } elseif (($rate = self::$xchg_service->getExchangeRate($this->currency, $currency)) === false) {
                    throw new \Exception(sprintf(
                        'Unable to determine exchange rate for "%s/%s"',
                        $this->currency,
                        $currency
                    ));
                }
            }

            $this->mul($rate);

            $old_currency = $this->currency;
            $this->currency = $currency;
            
            return $this;
        }
        
        /**
         * Add VAT to amount of money. The new value is stored in the money object.
         *
         * @octdoc  m:money/addVat
         * @param   float       $vat                Amount of VAT to add.
         * @return  \org\octris\core\type\money     Instance of current money object.
         *
         * @todo    Think about whether it might be useful to store VAT amount in money object and
         *          whether it would be nice to have methods like "getBtto", "getNet", etc.
         */
        public function addVat($vat)
        /**/
        {
            $this->mul(1 + $vat / 100);

            return $this;
        }

        /**
         * Substract discount from amount of money. The new value is stored in the money object.
         *
         * @octdoc  m:money/subDiscount
         * @param   float       $discount           Discount to substract from amount.
         * @return  \org\octris\core\type\money     Instance of current money object.
         */
        public function subDiscount($discount)
        /**/
        {
            $this->mul(1 - $discount / 100);

            return $this;
        }
    }
}

   