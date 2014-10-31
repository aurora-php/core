<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Type\Money;

/**
 * Interface for classes implementing a money exchange service.
 *
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface exchange_if
{
    /**
     * Return exchange rate between a source and a target currency.
     *
     * @param   string              $cur_source             Source currency (ISO 4217).
     * @param   string              $cur_target             Target currency (ISO 4217).
     * @return  float                                       Exchange rate.
     */
    public function getExchangeRate($cur_source, $cur_target);
    /**/
}
