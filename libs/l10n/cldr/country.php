<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\l10n\cldr {
    /**
     * Access territories related CLDR data.
     *
     * @octdoc      c:l10n/territories
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class country
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
            $data = \octris\core\l10n\cldr::getData('territories', $lc);
            
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
            $tmp = \octris\core\l10n\cldr::getSupplementalData('territoryContainment');
            $tmp = array_intersect_key($tmp, array_flip($codes));
            $tmp = array_unique(array_reduce($tmp, function ($carry, $item) {
                return array_merge($carry, $item);
            }, array()));
            
            if (is_null($lc)) {
                $lc = \octris\core\l10n::getInstance()->getLocale();
            }

            $data = \octris\core\l10n\cldr::getData('territories', $lc);
            $data = array_intersect_key($data['localeDisplayNames']['territories'], array_flip($tmp));
            
            $data = new \octris\core\type\collection($data);
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
            $tmp = \octris\core\l10n\cldr::getSupplementalData('territoryContainment');
            $tmp = array_unique(array_reduce($tmp, function ($carry, $item) {
                return array_merge($carry, $item);
            }, array()));
            
            if (is_null($lc)) {
                $lc = \octris\core\l10n::getInstance()->getLocale();
            }

            $data = \octris\core\l10n\cldr::getData('territories', $lc);
            $data = array_intersect_key($data['localeDisplayNames']['territories'], array_flip($tmp));
            
            $data = new \octris\core\type\collection($data);
            $data->asort(new \Collator($lc));
                
            return $data;
        }
    }
}
