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
     * Interface for classes implementing a money exchange service.
     *
     * @octdoc      i:cache/exchange_if
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface exchange_if
    /**/
    {
        /**
         * Return exchange rate between a source and a target currency.
         *
         * @octdoc  m:exchange_if/getExchangeRate
         * @param   string              $cur_source             Source currency (ISO 4217).
         * @param   string              $cur_target             Target currency (ISO 4217).
         * @return  float                                       Exchange rate.
         */
        public function getExchangeRate($cur_source, $cur_target);
        /**/
    }
}
