<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\L10n\Cldr;

/**
 * Access territories related CLDR data.
 *
 * @octdoc      c:l10n/territories
 * @copyright   copyright (c) 2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Country
{
    /**
     * Return name of country by country code.
     *
     * @octdoc  m:country/getByCode
     * @param   string              $code                   Country code.
     * @param   string              $lc                     Optional locale, defaults to current l10n setting.
     * @return  string                                      Localized name of country.
     */
    public static function getByCode($code, $lc = null)
    {
        $data = \Octris\Core\L10n\Cldr::getData('territories', $lc);

        return (isset($data['localeDisplayNames']['territories'][$lc])
                ? $data['localeDisplayNames']['territories'][$lc]
                : null);
    }

    /**
     * Return list of countries by territory codes.
     *
     * @octdoc  m:country/getListByTerritories
     * @param   array               $codes                  Territory code.
     * @param   string              $lc                     Optional locale, defaults to current l10n setting.
     * @return  array                                       Localized names of countries.
     */
    public static function getListByTerritories(array $codes, $lc = null)
    {
        $tmp = \Octris\Core\L10n\Cldr::getSupplementalData('territoryContainment');
        $tmp = array_intersect_key($tmp, array_flip($codes));
        $tmp = array_unique(array_reduce($tmp, function ($carry, $item) {
            return array_merge($carry, $item);
        }, array()));

        if (is_null($lc)) {
            $lc = \Octris\Core\L10n::getInstance()->getLocale();
        }

        $data = \Octris\Core\L10n\Cldr::getData('territories', $lc);
        $data = array_intersect_key($data['localeDisplayNames']['territories'], array_flip($tmp));

        $data = new \Octris\Core\Type\Collection($data);
        $data->asort(new \Collator($lc));

        return $data;
    }

    /**
     * Return list of all countries.
     *
     * @octdoc  m:country/getList
     * @param   string              $lc                     Optional locale, defaults to current l10n setting.
     * @return  array                                       Localized names of countries.
     */
    public static function getList($lc = null)
    {
        $tmp = \Octris\Core\L10n\Cldr::getSupplementalData('territoryContainment');
        $tmp = array_unique(array_reduce($tmp, function ($carry, $item) {
            return array_merge($carry, $item);
        }, array()));

        if (is_null($lc)) {
            $lc = \Octris\Core\L10n::getInstance()->getLocale();
        }

        $data = \Octris\Core\L10n\Cldr::getData('territories', $lc);
        $data = array_intersect_key($data['localeDisplayNames']['territories'], array_flip($tmp));

        $data = new \Octris\Core\Type\Collection($data);
        $data->asort(new \Collator($lc));

        return $data;
    }
}
