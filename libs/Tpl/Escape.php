<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\Tpl;

/**
 * Implements static methods for auto-escaping functionality.
 *
 * @copyright   copyright (c) 2012-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 *
 * @ref https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet
 * @ref https://www.owasp.org/index.php/DOM_based_XSS_Prevention_Cheat_Sheet
 * @ref https://wiki.php.net/rfc/escaper
 */
class Escape
{
    /**
     * Escape attribute name within a tag.
     *
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
     * Escape content to put into HTML comment.
     *
     * @param   string              $str                String to escape.
     * @return  string                                  Escaped string.
     */
    public static function escapeHtmlComment($str)
    {
        return preg_replace('/^([>!-])/', ' $1', str_replace('-', '- ', $str));

        return $str;
    }

    /**
     * Escape javascript.
     *
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
