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
     * Improves DateTime functionality. Contrary to the DateTime class of PHP, 
     * which this class extends, the constructor of this class accepts integer
     * timestamps and float timestamps with microseconds fraction, too. The other
     * problem this class fixes is, that DateTime does not set a timezone for
     * timestamps.
     *
     * @octdoc      c:type/number
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class datetime extends \DateTime
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:datetime/__construct
         */
        public function __construct($time = 'now', \DateTimeZone $timezone = null)
        /**/
        {
            if (is_int($time)) {
                // unix timestamp
                $time = '@' . $time;
            } elseif (is_float($time)) {
                // unix timestamp with microseconds
                $tmp = explode('.', $time);

                $time = date('Y-m-d H:i:s.' . $tmp[1], $tmp[0]);
            }
            
            parent::__construct($time, $timezone);
            
            if (substr($time, 0, 1) == '@') {
                // DateTime constructor will not use the timezone, if a timestamp is specified
                $timezone = (is_null($timezone) 
                             ? new \DateTimeZone(ini_get('date.timezone'))
                             : $timezone);
                
                $this->setTimezone($timezone);
            }
        }

        /**
         * Convert object to string.
         *
         * @octdoc  m:datetime/__toString
         * @return  string                              String representation of object.
         */
        public function __toString()
        /**/
        {
            return $this->format('U.u');
        }

        /**
         * Create new instance of datetime from specified format. We can't use the method of \DateTime,
         * because it would create a new instance of \DateTime and not of this child class.
         *
         * @octdoc  m:datetime/createFromFormat
         */
        public static function createFromFormat($format, $time, \DateTimeZone $timezone = null)
        /**/
        {
            $data = date_parse_from_format($format, $time);
            $now  = getdate();

            $time = mktime(
                ($data['hour'] === false ? $now['hours'] : $data['hour']),
                ($data['minute'] === false ? $now['minutes'] : $data['minute']),
                ($data['second'] === false ? $now['seconds'] : $data['second']),
                ($data['month'] === false ? $now['mon'] : $data['month']),
                ($data['day'] === false ? $now['mday'] : $data['day']),
                ($data['year'] === false ? $now['year'] : $data['year'])
            ) + $data['fraction'];
            
            return new static($time, $timezone);
        }
    }
}
