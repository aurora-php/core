<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\core\tpl;

/**
 * Implements static methods for auto-escaping functionality.
 *
 * @octdoc      c:tpl/escape
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 *
 * @ref https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet
 * @ref https://www.owasp.org/index.php/DOM_based_XSS_Prevention_Cheat_Sheet
 * @ref https://wiki.php.net/rfc/escaper
 */
class escape
{
    /**
     * Escape attribute name within a tag.
     *
     * @octdoc  m:escape/escapeAttribute
     * @param   string              $str                String to escape.
     * @return  string                                  Escaped string.
     */
    public static function escapeAttribute($str)
    {
        return $str;
    }

    /**
     * Escape HTML tag attribute.
     *
     * @octdoc  m:escape/escapeAttributeValue
     * @param   string              $str                String to escape.
     * @return  string                                  Escaped string.
     */
    public static function escapeAttributeValue($str)
    {
        return $str;
    }

    /**
     * Escape content to put into CSS context.
     *
     * @octdoc  m:escape/escapeCss
     * @param   string              $str                String to escape.
     * @return  string                                  Escaped string.
     */
    public static function escapeCss($str)
    {
        return $str;
    }

    /**
     * Escape content to put into HTML context to prevent XSS attacks.
     *
     * @octdoc  m:escape/escapeHtml
     * @param   string              $str                String to escape.
     * @return  string                                  Escaped string.
     */
    public static function escapeHtml($str)
    {
        $str = str_replace(
            array('&', '<', '>', '"', "'", '/'),
            array('&amp;', '&lt;', '&gt;', '&quot;', '&#x27;', '&#x2F;'),
            $str
        );

        return $str;
    }

    /**
     * Escape javascript.
     *
     * @octdoc  m:escape/escapeJs
     * @param   string              $str                String to escape.
     * @return  string                                  Escaped string.
     */
    public static function escapeJavascript($str)
    {
        return $str;
    }

    /**
     * Escape URI attribute value.
     *
     * @octdoc  m:escape/escapeUri
     * @param   string              $str                String to escape.
     * @return  string                                  Escaped string.
     */
    public static function escapeUri($str)
    {
        if (preg_match('/^javascript:/i', $str)) {
            // switch to javascript escaping instead
            $str = 'javascript:' . $this->escapeJavascript(sunstr($str, 11));
        } else {

        }

        return $str;
    }
}

