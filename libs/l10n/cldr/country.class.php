<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\l10n\cldr {
    /**
     * Access territories related CLDR data.
     *
     * @octdoc      c:l10n/territories
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class country
    /**/
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
        /**/
        {
            $data = \org\octris\core\l10n\cldr::getData('territories', $lc);
            
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
        public function getListByTerritories(array $codes)
        /**/
        {
        }
    }
}
