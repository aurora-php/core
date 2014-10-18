<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core {
    /**
     * Localisation library.
     *
     * @octdoc      c:core/l10n
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class l10n
    /**/
    {
        /**
         * Instance of l10n class for singleton pattern.
         *
         * @octdoc  p:l10n/$instance
         * @type    \octris\core\l10n
         */
        private static $instance = null;
        /**/

        /**
         * Locale string.
         *
         * @octdoc  p:l10n/$lc
         * @type    string
         */
        protected $lc = null;
        /**/

        /**
         * Stores language codes for restoreLocale
         *
         * @octdoc  p:l10n/$lc_mem
         * @type    array
         */
        protected $lc_mem = array();
        /**/

        /**
         * Gettext compiler cache -- an array -- is only used, if a caching backend is not set.
         *
         * @octdoc  p:l10n/$compiler_cache
         * @type    array
         * @see     l10n::setCache
         */
        protected $compiler_cache = array();
        /**/

        /**
         * L10n caching backend.
         *
         * @octdoc  p:l10n/$cache
         * @type    \octris\core\cache
         */
        protected static $cache = null;
        /**/

        /**
         * Directory of dictionary
         *
         * @octdoc  p:l10n/$directory
         * @type    string
         */
        protected $directory = '';
        /**/

        /**
         * Bound gettext domains.
         *
         * @octdoc  p:l10n/$domains
         * @type    array
         */
        protected $domains = array();
        /**/

        /**
         * Protected constructor and magic clone method. L10n is a singleton.
         *
         * @octdoc  m:l10n/__construct
         */
        protected function __construct() {
            $this->setLocale(\Locale::getDefault());
        }
        protected function __clone() {}
        /**/

        /**
         * Directory to lookup dictionary in.
         *
         * @octdoc  m:l10n/setDirectory
         * @param   string      $directory      Name of directory to set for looking up dictionary.
         */
        public function setDirectory($directory)
        /**/
        {
            $this->directory = $directory;
        }

        /**
         * Return instance of localization class.
         *
         * @octdoc  m:l10n/getInstance
         * @return  \octris\core\l10n       Instance of localization class.
         */
        public static function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }
            
            return self::$instance;
        }

        /**
         * Set caching backend for l10n.
         *
         * @octdoc  m:l10n/setCache
         * @param   \octris\core\cache      $cache          Instance of caching backend to use.
         */
        public static function setCache(\octris\core\cache $cache)
        /**/
        {
            self::$cache = $cache;
        }

        /**
         * Return instance of caching backend.
         *
         * @octdoc  m:l10n/getCache
         * @return  \octris\core\cache                      Instance of caching backend l10n uses.
         */
        public static function getCache()
        /**/
        {
            return self::$cache;
        }

        /**
         * Change locale setting for application.
         *
         * @octdoc  m:l10n/setLocale
         * @param   string      $locale         Localization string in the form of language_COUNTRY (e.g.: de_DE, en_US, ...).
         * @return  string                      Returns old localisation setting.
         */
        public function setLocale($locale)
        /**/
        {
            if (($pos = strpos($locale, '.')) !== false) {
                $locale = substr($locale, 0, $pos);
            }
            
            $ret      = $this->lc;
            $this->lc = $locale;

            array_push($this->lc_mem, $ret);

            // putenv('LANG=' . $locale);
            // putenv('LC_MESSAGES=' . $locale);
            setlocale(LC_MESSAGES, $locale);

            $this->addTextDomain('messages', $this->directory);

            return $ret;
        }

        /**
         * Get current localisation setting.
         *
         * @octdoc  m:l10n/getLocale
         * @return  string                      Current localization setting in the form of language_COUNTRY (e.g.: de_DE, en_US, ...).
         */
        public function getLocale()
        /**/
        {
            return $this->lc;
        }

        /**
         * Return language code from current set locale or from specified locale.
         *
         * @octdoc  m:l10n/getLanguageCode
         * @param   string      $code           Optional code to parse.
         * @return  string                      Language code.
         */
        public function getLanguageCode($code = null)
        /**/
        {
            $parts = explode('_', (is_null($code) ? $this->lc : $code));

            return strtolower($parts[0]);
        }

        /**
         * Return country code from current set locale or form specified locale.
         *
         * @octdoc  m:l10n/getCountryCode
         * @param   string      $code           Optional code to parse.
         * @return  string                      Country code.
         */
        public function getCountryCode($code = null)
        /**/
        {
            $parts = explode('_', (is_null($code) ? $this->lc : $code));

            return strtoupper(array_pop($parts));
        }

        /**
         * One level restoring locale setting, when a setting was overwritten using setLocale.
         *
         * @octdoc  m:l10n/restoreLocale
         */
        public function restoreLocale()
        /**/
        {
            if (count($this->lc_mem) > 0) {
                $this->setLocale(array_pop($this->lc_mem));
            }
        }

        /**
         * Money formatter.
         *
         * @octdoc  m:l10n/monf
         * @todo    implementation
         * @param   mixed           $money              Float value as amount or instance of \octris\core\type\money
         * @return  string                              Formatted money.
         */
        public function monf($money, $currency = 'EUR')
        /**/
        {
            return \NumberFormatter::create($this->lc, \NumberFormatter::CURRENCY)->formatCurrency($money, $currency);
        }

        /**
         * Number formatter.
         *
         * @octdoc  m:l10n/numf
         * @todo    implementation
         * @param   mixed           $number             Numerical value to format.
         * @return  string                              Formatted number.
         */
        public function numf($number)
        /**/
        {
            return \NumberFormatter::create($this->lc, \NumberFormatter::DECIMAL)->format($number);
        }

        /**
         * Percentage formatter.
         *
         * @octdoc  m:l10n/perf
         * @param   mixed           $number             Numerical value to format.
         * @return  string                              Formatted number.
         */
        public function perf($percentage)
        /**/
        {
            return \NumberFormatter::create($this->lc, \NumberFormatter::PERCENT)->format($percentage);
        }

        /**
         * Date formatter. Can either be an ISO date string, a timestamp or
         * a PHP DateTime object.
         *
         * @octdoc  m:l10n/datef
         * @todo    Implementation.
         * @param   mixed           $datetime           Date.
         * @param   int             $format             Optional formatting type.
         * @return  string                              Formatted date.
         */
        public function datef($datetime, $format = \IntlDateFormatter::SHORT)
        /**/
        {
            return \IntlDateFormatter::create($this->lc, $format, $format)->format($datetime);
        }

        /**
         * Value enumeration.
         *
         * @octdoc  m:l10n/enum
         * @param   int             $value              Number of element to retrieve.
         * @param   ...             ...$items           Arbitrary amount of items.
         * @return  string                              The value of the item of position 'value' or an empty string.
         */
        public function enum($value, ...$items) 
        /**/
        {
            return (!array_key_exists($value - 1, $items) 
                    ? ''
                    : $items[$value - 1]);
        }        

        /**
         * If parameter 'test' ist bool true, the parameter 'first' will
         * be returnes, otherwise the parameter 'second' will be returned.
         *
         * @octdoc  m:l10n/yesno
         * @param   mixed           $test               Value to test.
         * @param   string          $first              First possible return value.
         * @param   string          $second             Second possible return value.
         * @return  string                              Return value according to 'test'.
         */
        public function yesno($test, $first, $second = '')
        /**/
        {
            return (!!$test ? $first : $second);
        }

        /**
         * Quantisation. The string parameters 'first', 'second' and 'third'
         * may contain a %d placeholder (@see sprintf) to include the value
         * of 'test'.
         *
         * @octdoc  m:l10n/quant
         * @param   int/float       $test               Value to test.
         * @param   string          $first              Return value if 'test' == 1 or 'second' / 'third' are not set.
         * @param   string          $second             Optional return value if 'test' != 1.
         * @param   string          $third              Optional return value if 'test' == 0.
         * @return  string                              Return value according to 'test'.
         */
        public function quant($test, $first, $second = null, $third = null)
        /**/
        {
            $return = $first;

            if ($test == 0 && !is_null($third)) {
                $return = $third;
            } elseif ($test != 1 && !is_null($second)) {
                $return = $second;
            }

            return \octris\core\type\string::sprintf($return, $test);
        }

        /**
         * Writes out a list of values separated by a specified character
         * (default: ', ') and the last one by a string (eg: 'and' or 'or').
         *
         * @octdoc  m:l10n/comify
         * @param   array           $list               List of elements to concatenate.
         * @param   string          $word               Word to concatenate last item with.
         * @param   string          $sep                Optional separator.
         * @return  string                              Concatenated list.
         */
        public function comify(array $list, $word, $sep = ', ')
        /**/
        {
            $return = '';

            if (count($list) > 0) {
                $last = array_pop($list);

                $return = implode($word, array(implode($sep, $list), $last));
            }

            return $return;
        }

        /**
         * Returns text according to specified gender.
         *
         * @octdoc  m:l10n/gender
         * @param   int/string      $gender             Gender (one of: mM1fFwW2nN0)
         * @param   string          $undefined          String to return if gender is not specified ('gender' one of 'n', 'N' or '0').
         * @param   string          $male               String to return if gender is male ('gender' one of 'm', 'M' or '1').
         * @param   string          $female             String to return if gender is female ('gender' one of 'f', 'F' or '2').
         * @return  string                              String according to specified gender.
         */
        public function gender($gender, $undefined, $male, $female)
        /**/
        {
            switch (strtoupper($gender)) {
            case 'M':
            case '1':
                $return = $male;
                break;
            case 'F':
            case 'W':
            case '2':
                $return = $female;
                break;
            case 'N':
            case '0':
            default:
                $return = $undefined;
                break;
            }

            return $return;
        }

        /**
         * Add a gettext domain. Note that the first domain added will be set as
         * default domain. This can be changed by setting a domain using the 
         * 'setDefaultDomain' method.
         *
         * @octdoc  m:l10n/addTextDomain
         * @see     setDefaultDomain
         * @param   string          $domain             Name of domain.
         * @param   string          $directory          Base directory for localized text packages.
         * @param   string          $codeset            Optional codeset of text domain.
         */
        public function addTextDomain($domain, $directory, $codeset = 'UTF-8')
        /**/
        {
            bind_textdomain_codeset($domain, $codeset);
            bindtextdomain($domain, $directory);

            if (count($this->domains) == 0) {
                textdomain($domain);
            }
            
            $this->domains[] = $domain;
        }

        /**
         * Set the default gettext domain. Note, that a domain must have been 
         * already added using the 'addTextDomain' method.
         *
         * @octdoc  m:l10n/setDefaultDomain
         * @see     addTextDomain
         * @param   string          $domain             Name of domain.
         * @return  string                              The domain that was set before.
         */
        public function setDefaultDomain($domain)
        /**/
        {
            return textdomain($domain);
        }
        
        /**
         * Return the current set default text domain. Note, that a domain must have been 
         * already added using the 'addTextDomain' method.
         *
         * @octdoc  m:l10n/getDefaultDomain
         * @see     addTextDomain
         */
        public function getDefaultDomain()
        /**/
        {
            return textdomain(null);
        }
        
        /**
         * Translate message with currently set dictionary.
         *
         * @octdoc  m:l10n/translate
         * @param   string          $msg                Message to translate.
         * @param   array           $args               Optional parameters for inline functions.
         * @param   string          $domain             Optional text domain to use.
         * @return  string                              Translated text or text from 'msg' parameter, if no translation was found.
         */
        public function translate($msg, array $args = array(), $domain = null)
        /**/
        {
            // get localized text from dictionary
            if ($msg !== '') {
                $msg = $this->lookup($msg, $domain);
            }

            // compile included function calls if not in cache
            if (!is_null(self::$cache)) {
                $cache = self::$cache;
            } else {
                $cache =& $this->compiler_cache;
            }
            
            $key = $this->lc . '.' . $msg;
            
            if (!isset($cache[$key])) {
                $cache[$key] = $this->compile($msg);
            }

            return $cache[$key]($this, $args);
        }

        /**
         * Lookup a message in the dictionary and return it's translation.
         * This method differs from '__' and 'translate' in that it won't 
         * compile any inline functions.
         *
         * @octdoc  m:l10n/lookup
         * @param   string          $msg                Message to lookup
         * @param   string          $domain             Optional text domain to use.
         * @return  string                              Translated message.
         */
        public function lookup($msg, $domain = null)
        /**/
        {
            $out = '';
            
            if ($msg !== '') {
                $out = (is_null($domain)
                        ? gettext($msg)
                        : dgettext($domain, $msg));
            }
            
            if ($out === '') {
                $out = $msg;
            }
            
            return $out;
        }

        /**
         * Gettext message compiler. It's purpose is to transform embedded
         * functions into PHP code.
         *
         * @octdoc  m:l10n/compile
         * @param   string          $msg                Message to compile.
         * @return  callback                            Created callback.
         */
        protected function compile($msg)
        /**/
        {
            $fn = array('comify', 'enum', 'monf', 'numf', 'perf', 'datef', 'gender', 'quant', 'yesno');
            
            $mem     = $msg;
            $msg     = "'" . addcslashes($msg, "'") . "'";
            $cnt     = 0;
            $pattern = '/\[(?:(?P<cmd>[a-z]+), *)?_(?P<arg>\d+)(?:, *(?P<str>.*?))?(?<!\\\)\]/s';

            $msg = preg_replace_callback($pattern, function($m) use ($fn) {
                $cmd = (isset($m['cmd']) ? $m['cmd'] : '');
                $arg = $m['arg'];

                if ($cmd != '') {
                    if (!in_array($cmd, $fn)) {
                        throw new \Exception(sprintf('undefined command "%s"', $cmd));
                    }
                    
                    $tmp = array_map(function($arg) {
                        return "'" . trim($arg) . "'";
                    }, (isset($m['str']) ? preg_split('/(?<!\\\),/', $m['str']) : array()));

                    array_unshift($tmp, "\$args[" . ($arg - 1) . "]");

                    $code = "' . \$obj->" . $cmd . "(" . implode(', ', $tmp) . ") . '";
                } else {
                    $code = "' . \$args[" . ($arg - 1) . "] . '";
                }

                return $code;
            }, $msg, -1, $cnt);

            if ($cnt == 0) {
                return function($obj, $args) use ($mem) { return $mem; };
            } else {
                dprint($msg);
                
                return create_function('$obj, $args', 'return ' . $msg . ';');
            }
        }
    }
}
