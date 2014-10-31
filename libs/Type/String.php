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
 * Static class providing UTF-8 safe string functions.
 *
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 *
 * @todo        convert match/replace methods to use preg_match / replace? see b2u helper method
 *              for using PREG_MATCH_CAPTURE_OFFSET.
 */
class String
{
    /**
     * Various constants.
     *
     */
    const T_CASE_UPPER       = MB_CASE_UPPER;
    const T_CASE_LOWER       = MB_CASE_LOWER;
    const T_CASE_TITLE       = MB_CASE_TITLE;
    const T_CASE_UPPER_FIRST = 1000;
    const T_CASE_LOWER_FIRST = 1001;
    
    /** make class static **/
    protected function __construct() {}

    /**
     * This is a helper method to convert byte offsets to utf8 character units. This is useful
     * for example when working with PREG_MATCH_CAPTURE_OFFSET to convert the byte-offset to
     * utf8 character unit offset.
     *
     * @param   string      $string         String to calculate character units for.
     * @param   int         $byte_offset    Byte offset to convert.
     * @return  int                         Character units.
     */
    public static function b2u($string, $byte_offset)
    {
        return mb_strlen(substr($string, 0, $byte_offset), 'UTF-8');
    }

    /**
     * Return a specific character.
     *
     * @param   int         $chr            Code of the character to return.
     * @return  string                      The specified character.
     */
    public static function chr($chr)
    {
        return mb_convert_encoding('&#' . (int)$chr . ';', 'UTF-8', 'HTML-ENTITIES');
    }

    /**
     * This implements a helper function to pad an string or ID to a specified lenght
     * and chunk it using a specified chunk length. Note, that this function requires
     * that 'pad' is a multiple of 'len', because each chunk needs to be of the same
     * length. Note further, that resulting string get's an extra 'chunk_char' appended.
     * This function is especially useful for creating nested numeric path names, see
     * the following example using the default arguments:
     *
     * string: 123456
     * result: 000/123/456/
     *
     * The padding parameter 'pad' allows for padding or cutting the string -- according
     * to the number compared to the length of the string to pad/cut. The following
     * are the rules:
     *
     * * $pad < 0 -- padding/cutting on the left
     * * $pad > 0 -- padding/cutting on the right
     * * $pad = 0 -- no padding / cutting
     * * abs($pad) < len($string) -- the string will be cut
     * * abs($pad) > len($string) -- the string will be padded
     *
     * The side off cutting is specified through the sign of the 'pad' parameter:
     *
     * * $pad < 0 && abs($pad) < len($string) -- the string get's cut on the left
     * * $pad > 0 && abs($pad) < len($string) -- the string get's cut on the right
     *
     * @param   int|string  $string         String or number to chunk.
     * @param   int         $pad            Optional number of characters to pad to.
     * @param   int         $chunk_len      Optional length of each chunk.
     * @param   string      $pad_char       Optional character for padding.
     * @param   string      $chunk_char     Optional character for chunking.
     * @return  string                      Chunked string.
     */
    public static function chunk_id($string, $pad = 9, $chunk_len = 3, $pad_char = '0', $chunk_char = '/')
    {
        $abs       = abs($pad);
        $chunk_len = ($chunk_len > $abs ? $abs : $chunk_len);
        $pad_char  = substr($pad_char, 0, 1);

        if ($abs % $chunk_len != 0) {
            throw new \Exception("'pad' ($pad) is not divisable by 'chunk_len' ($chunk_len)");
        } else {
            $format = sprintf(
                '%%\'%s%s%d.%ds',
                $pad_char,
                ($pad < 0 ? '-' : ''),
                $abs,
                $abs
            );

            $string = self::sprintf($format, $string);

            return self::chunk_split($string, $chunk_len, $chunk_char);
        }
    }

    /**
     * Split a string into smaller chunks.
     *
     * @param   string      $string         The string to be chunked.
     * @param   int         $chunk_len      The chunk length.
     * @param   string      $end            The line ending sequence.
     * @return  string                      The chunked string.
     */
    public static function chunk_split($string, $chunk_len = 76, $end = "\r\n")
    {
        return preg_replace_callback('/.{' . $chunk_len . '}/us', function ($m) use ($end) {
            return $m[0] . $end;
        }, $string) . (mb_strlen($string, 'UTF-8') % $chunk_len == 0 ? '' : $end);
    }

    /**
     * Performs case folding on a string.
     *
     * @param   string      $string         String to convert.
     * @param   int         $mode           Mode of case folding.
     * @return  string                      Converted string.
     */
    public static function convert_case($string, $mode)
    {
        switch ($mode) {
        case self::T_CASE_LOWER_FIRST:
            $return = preg_replace_callback('/^(.)/u', function ($m) {
                return mb_strtolower($m[1], 'UTF-8');
            }, $string);
            break;
        case self::T_CASE_UPPER_FIRST:
            $return = preg_replace_callback('/^(.)/u', function ($m) {
                return mb_strtoupper($m[1], 'UTF-8');
            }, $string);
            break;
        default:
            $return = mb_convert_case($string, $mode, 'UTF-8');
            break;
        }

        return $return;
    }

    /**
     * Regular expression match for multibyte string.
     *
     * @param   string      $pattern        The search pattern.
     * @param   string      $string         The search string.
     * @param   string      $options        If 'i' is specified for this parameter, the case will be ignored.
     * @return  array|bool                  The public static function returns substring of matched string. If no matches
     *                                      are found or an error happens, FALSE will be returned.
     */
    public static function match($pattern, $string, $options = '')
    {
        $m = array();

        mb_regex_encoding('UTF-8');

        if (strpos($options, 'i') !== false) {
            $return = mb_eregi($pattern, $string, $m);
        } else {
            $return = mb_ereg($pattern, $string, $m);
        }

        return ($return === false ? false : $m);
    }

    /**
     * Replace regular expression with multibyte support.
     *
     * @param   string      $pattern        The search pattern.
     * @param   string      $string         The search string.
     * @param   string      $options        Matching condition can be set by option parameter. If i is specified for this
     *                                      parameter, the case will be ignored. If x is specified, white space will be
     *                                      ignored. If m is specified, match will be executed in multiline mode and line
     *                                      break will be included in '.'. If p is specified, match will be executed in
     *                                      POSIX mode, line break will be considered as normal character. If e is
     *                                      specified, replacement string will be evaluated as PHP expression.
     * @return  string                      The resultant string on success, or FALSE on error.
     */
    public static function replace($pattern, $replacement, $string, $options = 'msr')
    {
        mb_regex_encoding('UTF-8');

        return mb_ereg_replace($pattern, $replacement, $string, $options);
    }

    /**
     * Make's the first character of a string lowercase.
     *
     * @param   string      $string         String to convert.
     * @return  string                      Converted string.
     */
    public static function lcfirst($string)
    {
        return self::convert_case($string, self::T_CASE_LOWER_FIRST);
    }

    /**
     * Make's the first character of a string uppercase.
     *
     * @param   string      $string         String to convert.
     * @return  string                      Converted string.
     */
    public static function ucfirst($string)
    {
        return self::convert_case($string, self::T_CASE_UPPER_FIRST);
    }

    /**
     * Cut a string after a specified number of characters. If possible and definied through the
     * parameter 'tolerance', this function will try to cut the string at a whitespace character.
     *
     * @param   string      $string         String to cut.
     * @param   int         $maxlen         Max length of string.
     * @param   string      $continue       Optional string to add if string is cut.
     * @param   int         $tolarance      Optional tolarance to catch space character.
     * @return  string                      Cut string.
     */
    public static function cut($string, $maxlen, $continue = ' ...', $tolarance = 10)
    {
        if (mb_strlen($string, 'UTF-8') <= $maxlen) {
            return $string;
        }

        $string = mb_substr($string, 0, $maxlen - mb_strlen($continue, 'UTF-8'), 'UTF-8');

        $pos = mb_strrpos($string, ' ', null, 'UTF-8');

        if ($pos !== false && mb_strlen($string, 'UTF-8') - $pos <= $tolarance) {
            $string = mb_substr($string, 0, $pos, 'UTF-8');
        }

        $string = trim($string);
        $chr    = mb_substr($string, -1, 1, 'UTF-8');

        if ($chr == '.' || $chr == '!' || $chr == '?') {
            return $string;
        }

        $string = rtrim($string, '-:,;') . $continue;

        return $string;
    }

    /**
     * Obliterate a string. Replace part of a string with a specified character, to make
     * it unusable by hiding information.
     *
     * @param   string      $string         String to obliterate.
     * @param   int         $len            Length the returned string should have.
     * @param   int         $readable       Optional number of characters to keep readable (>0 = from beginning; <0 = from end).
     * @param   string      $char           Character to use to make string unreadable.
     * @return  string                      Obliterated string.
     */
    public static function obliterate($string, $len, $readable = -2, $char = '*')
    {
        $return = '';

        if ($string != '') {
            $tmp = str_repeat($char, $len - abs($readable));

            $return = ($readable > 0
                    ? mb_substr($string, 0, $readable, 'UTF-8') . $tmp
                    : $tmp . mb_substr($string, $readable));
        }

        return $return;
    }

    /**
     * Shorten a string by cutting information from it. For example:
     *
     * makes: http://www.itunesreg[...]om/index.php
     * from:  http://www.itunesregistry.com/index.php
     *
     * If a string is longer than the specified 'maxlen', it get's shortened. If it's shorter or exactly
     * 'maxlen' characters long, it will be returned without any modification.
     *
     * @param   string      $string         String to shorten.
     * @param   int         $maxlen         Optional maximum length a string may have.
     * @param   int         $offset         Optional characters from the left that should be displayed before inserting characters.
     * @return  string                      Shortened string.
     */
    public static function shorten($string, $maxlen = 40, $offset = 20)
    {
        if (($len = mb_strlen($string, 'UTF-8')) <= $maxlen) {
            return $string;
        }

        return mb_substr($string, 0, $offset, 'UTF-8') . '[...]' .
               mb_substr($string, $len - ($maxlen - $offset - 5));
    }

    /**
     * Split multibyte string using regular expression.
     *
     * @param   string      $pattern        The regular expression pattern.
     * @param   string      $string         The string being split.
     * @return  array                       Array of splitted strings.
     */
    public static function split($pattern, $string)
    {
        mb_regex_encoding('UTF-8');

        return mb_split($pattern, $string);
    }

    /**
     * Case-insensitive string comparison.
     *
     * @param   string      $string1        The first string.
     * @param   string      $string2        The second string.
     * @param   Collator    $collator       Optional collator to use for comparision.
     * @return  int                         Returns < 0 if string1 is less than string2.
     *                                      Returns > 0 if string1 is greater than string2
     *                                      Returns 0 if both strings are equal.
     */
    public static function strcasecmp($string1, $string2, \Collator $collator = null)
    {
        return self::strcmp(self::strtolower($string1), self::strtolower($string2), $collator);
    }

    /**
     * String comparision.
     *
     * @param   string      $string1        The first string.
     * @param   string      $string2        The second string.
     * @param   Collator    $collator       Optional collator to use for comparision.
     * @return  int                         Returns < 0 if string1 is less than string2.
     *                                      Returns > 0 if string1 is greater than string2
     *                                      Returns 0 if both strings are equal.
     */
    public static function strcmp($string1, $string2, \Collator $collator = null)
    {
        $collator = $collator ?: new \Collator(\Octris\Core\L10n::getInstance()->getLocale());

        return $collator->compare($string1, $string2);
    }

    /**
     * Finds position of first occurrence of a string within another, case insensitive.
     *
     * @param   string      $string         String to return length for.
     * @param   string      $needle         The position counted from the beginning of haystack.
     * @param   int         $offset         The search offset. If it is not specified, 0 is used.
     * @return  int|bool                    Returns the numeric position of the first occurrence of needle in the
     *                                      haystack string. If needle is not found, it returns FALSE.
     */
    public static function stripos($string, $needle, $offset = 0)
    {
        return mb_stripos($string, $needle, $offset, 'UTF-8');
    }

    /**
     * Finds first occurrence of a string within another, case insensitive.
     *
     * @param   string      $string         The string from which to get the first occurrence of needle.
     * @param   string      $needle         The string to find in string.
     * @param   bool        $part           Determines which portion of haystack this public static function returns. If set to
     *                                      TRUE, it returns all of string from the beginning to the first occurrence
     *                                      of needle. If set to FALSE, it returns all of string from the first
     *                                      occurrence of needle to the end.
     * @return  string|bool                 Returns the portion of string, or FALSE if needle is not found.
     */
    public static function stristr($string, $needle, $part = false)
    {
        return mb_stristr($string, $needle, $part, 'UTF-8');
    }

    /**
     * Return length of the given string.
     *
     * @param   string      $string         String to return length for.
     */
    public static function strlen($string)
    {
        return mb_strlen($string, 'UTF-8');
    }

    /**
     * Find position of first occurrence of string in a string.
     *
     * @param   string      $string         String to return length for.
     * @param   string      $needle         The position counted from the beginning of haystack.
     * @param   int         $offset         The search offset. If it is not specified, 0 is used.
     * @return  int|bool                    Returns the numeric position of the first occurrence of needle in the
     *                                      haystack string. If needle is not found, it returns FALSE.
     */
    public static function strpos($string, $needle, $offset = 0)
    {
        return mb_strpos($string, $needle, $offset, 'UTF-8');
    }

    /**
     * Case-insensitive string comparison using natural sorting algorithm.
     *
     * @param   string      $string1        The first string.
     * @param   string      $string2        The second string.
     * @param   Collator    $collator       Optional collator to use for comparision.
     * @return  int                         Returns < 0 if string1 is less than string2.
     *                                      Returns > 0 if string1 is greater than string2
     *                                      Returns 0 if both strings are equal.
     */
    public static function strnatcasecmp($string1, $string2, \Collator $collator = null)
    {
        return self::strnatcmp(self::strtolower($string1), self::strtolower($string2), $collator);
    }

    /**
     * String comparison using natural sorting algorithm.
     *
     * @param   string      $string1        The first string.
     * @param   string      $string2        The second string.
     * @param   Collator    $collator       Optional collator to use for comparision.
     * @return  int                         Returns < 0 if string1 is less than string2.
     *                                      Returns > 0 if string1 is greater than string2
     *                                      Returns 0 if both strings are equal.
     */
    public static function strnatcmp($string1, $string2, \Collator $collator = null)
    {
        if ($collator) {
            $collator = clone($collator);
        } else {
            $collator = new \Collator(\Octris\Core\L10n::getInstance()->getLocale());
        }

        $collator->setAttribute(Collator::NUMERIC_COLLATION, Collator::ON);

        return self::strcmp($string1, $string2, $collator);
    }

    /**
     * Case-insensitive string comparison of the first n characters.
     *
     * @param   string      $string1        The first string.
     * @param   string      $string2        The second string.
     * @param   int         $length         Number of characters to use in the comparison.
     * @param   Collator    $collator       Optional collator to use for comparision.
     * @return  int                         Returns < 0 if string1 is less than string2.
     *                                      Returns > 0 if string1 is greater than string2
     *                                      Returns 0 if both strings are equal.
     */
    public static function strncasecmp($string1, $string2, $length, \Collator $collator = null)
    {
        return self::strncmp(self::strtolower($string1), self::strtolower($string2), $length);
    }

    /**
     * String comparison of the first n characters.
     *
     * @param   string      $string1        The first string.
     * @param   string      $string2        The second string.
     * @param   int         $length         Number of characters to use in the comparison.
     * @param   Collator    $collator       Optional collator to use for comparision.
     * @return  int                         Returns < 0 if string1 is less than string2.
     *                                      Returns > 0 if string1 is greater than string2
     *                                      Returns 0 if both strings are equal.
     */
    public static function strncmp($string1, $string2, $length, \Collator $collator = null)
    {
        $string1 = self::substr($string1, 0, $length);
        $string2 = self::substr($string2, 0, $length);

        return self::strcmp($string1, $string2, $collator);
    }

    /**
     * Pad a string to a certain length with another string.
     *
     * @param   string      $string         String to pad.
     * @param   int         $length         Length to pad string to.
     * @param   string      $chr            Optional character to use for padding.
     * @param   string      $type           Optional argument can be STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH.
     * @return  string                      Padded string.
     */
    public static function str_pad($string, $length, $chr = ' ', $type = STR_PAD_RIGHT)
    {
        if (!in_array($type, array(STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH))) {
            $type = STR_PAD_RIGHT;
        }

        $diff = strlen($string) - mb_strlen($string, 'UTF-8');

        return str_pad($string, $length + $diff, $chr, $type);
    }

    /**
     * Randomly shuffles a string.
     *
     * @param   string      $string         The string to shuffle.
     * @return  string                      The shuffled string.
     */
    public static function str_shuffle($string)
    {
        return implode('', array_shuffle(preg_split('//us', $string)));
    }

    /**
     * Convert a string to an array.
     *
     * @param   string      $string         The string to be chunked.
     * @param   int         $split_length   Optional maximum length of the chunk.
     * @return  array                       The chunked string.
     */
    public static function str_split($string, $split_length = 1)
    {
        $m = array();

        return (preg_match_all('/.{1,' . $len . '}/us', $str, $m)
                ? $m[0]
                : array());
    }

    /**
     * Reverse a string.
     *
     * @param   string      $string         The string to be reversed.
     * @return  string                      Reversed string.
     */
    public static function strrev($string)
    {
        return implode('', array_reverse(preg_split('//us', $string)));
    }

    /**
     * Find position of last occurrence of a string in a string.
     *
     * @param   string      $string         String to return length for.
     * @param   string      $needle         The string to find in haystack.
     * @param   int         $offset         May be specified to begin searching an arbitrary number of characters
     *                                      into the string. Negative values will stop searching at an arbitrary point
     *                                      prior to the end of the string.
     * @return  int|bool                    Returns the numeric position of the last occurrence of needle in the
     *                                      haystack string. If needle is not found, it returns FALSE.
     */
    public static function strrpos($string, $needle, $offset = null)
    {
        return mb_strrpos($string, $needle, $offset, 'UTF-8');
    }

    /**
     * Finds position of last occurrence of a string within another, case insensitive.
     *
     * @param   string      $string         String to return length for.
     * @param   string      $needle         The string to find in haystack.
     * @param   int         $offset         May be specified to begin searching an arbitrary number of characters
     *                                      into the string. Negative values will stop searching at an arbitrary point
     *                                      prior to the end of the string.
     * @return  int|bool                    Returns the numeric position of the last occurrence of needle in the
     *                                      haystack string. If needle is not found, it returns FALSE.
     */
    public static function strripos($string, $needle, $offset = null)
    {
        return mb_strripos($string, $needle, $offset, 'UTF-8');
    }

    /**
     * Finds first occurrence of a string within another.
     *
     * @param   string      $string         The string from which to get the first occurrence of needle.
     * @param   string      $needle         The string to find in string.
     * @param   bool        $part           Determines which portion of haystack this public static function returns. If set to
     *                                      TRUE, it returns all of string from the beginning to the first occurrence
     *                                      of needle. If set to FALSE, it returns all of string from the first
     *                                      occurrence of needle to the end.
     * @return  string|bool                 Returns the portion of string, or FALSE if needle is not found.
     */
    public static function strstr($string, $needle, $part = false)
    {
        return mb_strstr($string, $needle, $part, 'UTF-8');
    }

    /**
     * Make a string lowercase.
     *
     * @param   string      $string         The string being lowercased.
     * @return  string                      String with all alphabetic characters converted to lowercase.
     */
    public static function strtolower($string)
    {
        return mb_strtolower($string, 'UTF-8');
    }

    /**
     * Make a string uppercase.
     *
     * @param   string      $string         The string being uppercased.
     * @return  string                      String with all alphabetic characters converted to uppercase.
     */
    public static function strtoupper($string)
    {
        return mb_strtoupper($string, 'UTF-8');
    }

    /**
     * Get part of string.
     *
     * @param   string      $string         The string to extract a part from.
     * @param   int         $start          The first position used in string.
     * @param   int|null    $length         Optional length of the part to extract.
     */
    public static function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Comparison of two strings from an offset, up to length characters.
     *
     * @param   string      $string         The main string being compared.
     * @param   string      $compare        The secondary string being compared.
     * @param   int         $offset         The start position for the comparison. If negative, it starts counting from
     *                                      the end of the string.
     * @param   int         $length         Optional length of the comparison. The default value is the largest of the length
     *                                      of $string compared to the length of $compare less the offset.
     * @param   bool        $ignore_case    Optional, if set to TRUE, comparison is case insensitive.
     */
    public static function substr_compare($string, $compare, $offset, $length = null, $ignore_case = false)
    {
        if (is_null($length)) {
            $string = mb_substr($string, $offset);
        } else {
            $string  = mb_substr($string, $offset, $length);
            $compare = mb_substr($string, 0, $length);
        }

        return ($ignore_case ? strcasecmp($string, $compare) : strcmp($string, $compare));
    }

    /**
     * Count the number of substring occurences.
     *
     * @param   string      $string         The string being checked.
     * @param   string      $needle         The string being found.
     * @return  string                      The number of times the needle substring occurs in the haystack string.
     */
    public static function substr_count($string, $needle)
    {
        return mb_substr_count($string, $needle, 'UTF-8');
    }

    /**
     * Replace text within a portion of a string.
     *
     * @param   string      $string         The input string.
     * @param   string      $replacement    The replacement string.
     * @param   int         $start          If start is positive, the replacing will begin at the start'th offset
     *                                      into string. If start is negative, the replacing will begin at the
     *                                      start'th character from the end of string.
     * @param   int         $length         Optional length of portion of string which shall be replaced. If length
     *                                      is negative, it represents the number of characters from the end of string
     *                                      at which to stop replacing. If it is not given, then it will default to
     *                                      the length of the string. If length is zero then this public static function will have
     *                                      the effect of inserting replacement into string at the given start offset.
     */
    public static function substr_replace($string, $replacement, $start, $length = null)
    {
        if (is_null($length)) $length = strlen($string);

        return substr($string, 0, $start) .
                $replacement .
                substr($string, ($len < 0 ? max($start - strlen($string), $length) : $start + $length));
    }

    /**
     * Convert a specified string to 7bit.
     *
     * @param   string      $string         String to convert.
     * @return  string                      Converted string to 7bit.
     */
    public static function to7bit($string)
    {
        $string = mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8');
        $string = preg_replace(
            array('/&szlig;/', '/&(..)lig;/', '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
            array('ss', '$1', '$1'.'e', '$1'),
            $string
        );

        return $string;
    }

    /**
     * Replaces PHP's htmlentities to safely convert using specified encoding.
     *
     * @param   string      $string         String to convert.
     * @param   int         $quote_style    Optional parameter to define what will be done with 'single' and "double" quotes.
     * @return  string                      Converted string.
     */
    public static function htmlentities($string, $quote_style = ENT_COMPAT)
    {
        return \htmlentities($string, $quote_style, 'UTF-8') ;
    }

    /**
     *
     *
     * @param   string      $string         The input string.
     * @param   int         $quote_style    Optional parameter to define what will be done with 'single' and "double" quotes.
     * @return  string                      Converted string.
     */
    public static function html_entity_decode($string, $quote_style = ENT_COMPAT)
    {
        return \html_entity_decode($string, $quote_style, 'UTF-8');
    }


    /**
     * Convert special characters to HTML entities.
     *
     * @param   string      $string         String to convert.
     * @param   int         $quote_style    Optional parameter to define what will be done with 'single' and "double" quotes.
     * @return  string                      Converted string.
     */
    public static function htmlspecialchars($string, $quote_style)
    {
        return \htmlspecialchars($string, $quote_style, 'UTF-8');
    }

    /**
     * Convert a string to UTF-8
     *
     * @param   string      $string         String to convert.
     * @param   string      $encoding       Optional convert from this encoding to UTF-8.
     * @return  string                      Converted string.
     */
    public static function toUtf8($string, $encoding = 'ISO-8859-1')
    {
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', $encoding);

            if (!mb_check_encoding($content, 'UTF-8')) {
                trigger_error('unable to convert to UTF-8');
            }
        }

        return $string;
    }

    /**
     * Convert character encoding of a string.
     *
     * @param   string      $string         The string being encoded.
     * @param   string      $to_encoding    The type of encoding that str is being converted to.
     * @param   string      $from_encoding  Optional source encoding is specified by character code names before conversion.
     *                                      It is either an array, or a comma separated enumerated list. If from_encoding is not
     *                                      specified, the internal encoding will be used.
     * @return  string                      The encoded string.
     */
    public static function convert($string, $to_encoding, $from_encoding = null)
    {
        return mb_convert_encoding($string, $to_encoding, $from_encoding);
    }

    /**
     * Strip whitespace (or other characters) from the beginning of a string.
     *
     * @param   string      $string         The input string.
     * @param   string      $charlist       Optional characters to strip.
     * @return  string                      Stripped string.
     */
    public static function ltrim($string, $charlist = null)
    {
        if (is_null($charlist)) {
            $string = ltrim($string);
        } else {
            $regexp = '/^[' . preg_quote($charlist, '/') . ']+/u';
            $string = preg_replace($regexp, '', $string);
        }

        return $string;
    }

    /**
     * Strip whitespace (or other characters) from the end of a string.
     *
     * @param   string      $string         The input string.
     * @param   string      $charlist       Optional characters to strip.
     * @return  string                      Stripped string.
     */
    public static function rtrim($string, $charlist = null)
    {
        if (is_null($charlist)) {
            $string = rtrim($string);
        } else {
            $regexp = '/[' . preg_quote($charlist, '/') . ']+$/u';
            $string = preg_replace($regexp, '', $string);
        }

        return $string;
    }

    /**
     * Strip whitespace (or other characters) from the both start and end of a string.
     *
     * @param   string      $string         The input string.
     * @param   string      $charlist       Optional characters to strip.
     * @return  string                      Stripped string.
     */
    public static function trim($string, $charlist = null)
    {
        if (is_null($charlist)) {
            $string = trim($string);
        } else {
            $string = ltrim(rtrim($string, $charlist), $charlist);
        }

        return $string;
    }

    /**
     * Return a formatted string.
     *
     * @param   string      $format         Formatting pattern.
     * @param   mixed       ...$params      Additional optional parameters for pattern replacing in first parameter.
     * @return  string                      Returns a string produced according to the formatting string format.
     */
    public static function sprintf($format, ...$params)
    {
        return sprintf($format, ...$params);
    }

    /**
     * Return a formatted string.
     *
     * @param   string      $format         Formatting pattern.
     * @param   mixed       $args           Optional parameters for pattern replacing in first parameter.
     * @return  string                      Returns a string produced according to the formatting string format.
     */
    public static function vsprintf($format, array $args = array())
    {
        $idx = 0;

        $format = preg_replace_callback(
            '/(?<!%)%(\+?)(\'.|[0 ]|)(-?)([1-9][0-9]*|)(\.[1-9][0-9]*|)([bcdeEufFgGosxX])/u',
            function ($m) use ($args, &$idx) {
                list($return, $sign, $filler, $align, $size, $prec, $type) = $m;

                if ($type == 's') {
                    // string formatter
                    if (!isset($args[$idx])) {
                        die('argument not set for ' . $idx . "\n");
                    }

                    if (($diff = strlen($args[$idx]) - self::strlen($args[$idx], 'UTF-8')) > 0) {
                        if ($prec !== '') $prec = '.' . ((int)substr($prec, 1) + $diff);
                        if ($size !== '') $size = (int)$size + $diff;
                    }

                    $return = "%$sign$filler$align$size$prec$type";
                }

                ++$idx;

                return $return;
            },
            $format
        );

        return vsprintf($format, $args);
    }

    /**
     * Check if a specified string is valid UTF-8.
     *
     * @param   string      $string         String to validate.
     * @return  bool                        Returns true, if a string is valid UTF-8.
     */
    public static function isUtf8($string)
    {
        $tmp = mb_convert_encoding($string, 'UTF-8', 'UTF-8');

        return ($tmp == $string);

    }
}
