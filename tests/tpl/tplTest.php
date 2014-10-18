<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('octris/core/app/autoloader.php');

class l10n {
    function _($msg) {
        return $msg;
    }
    function gettext($msg) {
        return $msg;
    }
    function lookup($msg) {
        return $msg;
    }
}

$l10n = new l10n();

$tpl = new \octris\core\tpl();
$tpl->setL10n($l10n);
$tpl->addSearchPath(dirname(__FILE__) . '/../../tests/tpl/compiler/');
print $tpl->compile('tpl1.html');

