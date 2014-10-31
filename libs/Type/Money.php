<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Type;

/**
 * Money type.
 *
 * @copyright   copyright (c) 2010-2013 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Money extends \Octris\Core\Type\Number
{
    /**
     * Currency of money object (ISO 4217)
     *
     * @type    string
     */
    protected $currency = 'EUR';
    
    /**
     * Stores instance of money exchange class.
     *
     * @type    \octris\core\type\money\IExchange
     */
    protected static $xchg_service = null;
    
    /**
     * Stores money precision.
     *
     * @type    int
     */
    protected $precision;
    
    /**
     * Constructor. Note that a money object can have a currency, which is not bound to the
     * currently set locale. If a precision is specifed, the precision will only be used for
     * returning the money amount. For internal calculations the default precision will be
     * used.
     *
     * @param   float       $value      Optional value for money object without locale specific characters.
     * @param   string      $currency   Optional curreny (ISO 4217) to set.
     * @param   int         $precision  Optional precision to use.
     */
    public function __construct($value = 0, $currency = null, $precision = 2)
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
     * @param   string          $name           Name of property to set.
     * @param   mixed           $value          Value to set for property.
     */
    public function __set($name, $value)
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
     * @param   string          $name           Name of property to get.
     */
    public function __get($name)
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
     * @param   \Octris\Core\Type\Money\IExchange     $service    Instance of a money exchange service.
     */
    public static function setExchangeService(\Octris\Core\Type\Money\IExchange $service)
    {
        self::$xchg_service = $service;
    }

    /**
     * Allocate the amount of money between multiple targets.
     *
     * @param   array               $ratios         Ratios to allocate.
     * @return  array                               Array of objects of type \octris\core\type\money.
     */
    public function allocate(array $ratios)
    {
        $total  = (new \Octris\Core\Type\Number())->add($ratios);
        $remain = new \Octris\Core\Type\Number($this->value);
        $return = array();

        for ($i = 0, $cnt = count($ratios); $i < $cnt; ++$i) {
            $return[$i] = clone $this;
            $return[$i]->mul($ratios[$i])->div($total)->round($this->precision);

            $remain->sub($return[$i]);
        }

        $unit = (new \Octris\Core\Type\Number(10))->pow(-$this->precision);
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
     * @param   mixed               $num    Number to compare with.
     * @return  bool                        Returns true, if money objects are equal.
     */
    public function equals($num)
    {
        if (($return = (is_object($num) && $num instanceof \octris\core\type\money))) {
            $return = ($this->currency === $num->currency && parent::equals($num));
        }

        return $return;
    }

    /**
     * Convert money object to an other currency using specified exchange rate.
     *
     * @param   string      $currency           Currency to convert to.
     * @param   float       $rate               Optional exchange rate. The exchange rate -- if specified -- will
     *                                          prevent the call of any set exchange service callback.
     * @param   string      $old_currency       Optional parameter which get's filled from the method with the original currency of the money object.
     * @return  \octris\core\type\money     Instance of current money object.
     */
    public function exchange($currency, $rate = null, &$old_currency = null)
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
     * @param   float       $vat                Amount of VAT to add.
     * @return  \octris\core\type\money     Instance of current money object.
     *
     * @todo    Think about whether it might be useful to store VAT amount in money object and
     *          whether it would be nice to have methods like "getBtto", "getNet", etc.
     */
    public function addVat($vat)
    {
        $this->mul(1 + $vat / 100);

        return $this;
    }

    /**
     * Substract discount from amount of money. The new value is stored in the money object.
     *
     * @param   float       $discount           Discount to substract from amount.
     * @return  \octris\core\type\money     Instance of current money object.
     */
    public function subDiscount($discount)
    {
        $this->mul(1 - $discount / 100);

        return $this;
    }
}
