<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\type\money {
    /**
     * Implements the API of 'openexchangerates.org'.
     *
     * @octdoc      c:money/openexchangerates
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class openexchangerates implements \org\octris\core\type\money\exchange_if
    /**/
    {
        /**
         * Exchange rates.
         *
         * @octdoc  p:openexchangerates/$rates
         * @type    array|null
         */
        protected static $rates = null;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:openexchangerates/__construct
         */
        public function __construct()
        /**/
        {
        }

        /**
         * Load exchange rates from openexchangerates service.
         *
         * @octdoc  m:openexchangerates/loadExchangeRates
         * @param   bool                    $reload                 Whether to reload exchange rates, when they have already been loaded.
         */
        protected function loadExchangeRates($reload = false)
        /**/
        {
            if (is_null(self::$rates) || $reload) {
                $ch = curl_init('http://openexchangerates.org/latest.json');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $json = curl_exec($ch);
                curl_close($ch);

                // Decode JSON response:
                self::$rates = json_decode($json);
            }
        }

        /**
         * Return exchange rate between a source and a target currency.
         *
         * @octdoc  m:openexchangerates/getExchangeRate
         * @param   string              $cur_source             Source currency (ISO 4217).
         * @param   string              $cur_target             Target currency (ISO 4217).
         * @return  float                                       Exchange rate.
         */
        public function getExchangeRate($cur_source, $cur_target)
        /**/
        {
            $return = false;

            if ($cur_source == $cur_target) {
                $return = 1;
            } else {
                $this->loadExchangeRates();

                $rates =& self::$rates->rates;

                if (!isset($rates->$cur_source)) {
                    throw new \Exception(sprintf('Source currency "%s" is unknown', $cur_source));
                } elseif (!isset($rates->$cur_target)) {
                    throw new \Exception(sprintf('Target currency "%s" is unknown', $cur_target));
                } else {
                    if ($cur_source == 'USD') {
                        // base is USD -- exchange rate is directly available
                        $return = $rates->$cur_source;
                    } elseif ($cur_target == 'USD') {
                        // target is USD -- calculate inverse exchange rate
                        $return = (string)(new \org\octris\core\type\number(1))->div($rates->$cur_source);
                    } else {
                        // target/source are not USD -- calculate cross rate
                        $return = (string)(new \org\octris\core\type\number($rates->$cur_target))->div($rates->$cur_source);
                    }
                }
            }

            return $return;
        }
    }
}

