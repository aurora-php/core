<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core;

use Composer\Script\Event;

/**
 * Class for defining composer event targets.
 *
 * @copyright   copyright (c) 2017 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Composer
{
    /**
     * Tasks to perform after installation.
     * 
     * @param   \Composer\Script\Event  $event          Event object.
     */
    public static function postInstall(Event $event)
    {
        if (!$event->isDevMode()) {
            // compile templates in production environment
            $dirname = dirname(\Composer\Factory::getComposerFile());
            
            if (file_exists($dirname . '/etc/global.php')) {
                // compile templates
                require($dirname . '/etc/global.php');
                
                print "Running template compiler ...\n";
                
                $registry = \Octris\Core\Registry::getInstance();
                $tpl = $registry->createTemplate;

                $next_path = '';
                $total_time = 0;

                foreach ($tpl->getTemplatesIterator() as $file => $path) {
                    if ($next_path != $path) {
                        $next_path = $path;

                        printf("\n%s:\n", dirname($path));
                    }

                    printf("%s ... ", $file);

                    $start = microtime(true);

                    $tpl->compile($file, \Octris\Core\Tpl::ESC_HTML);

                    $end = microtime(true);

                    printf("%1.4fs\n", $end - $start);

                    $total_time += ($end - $start);
                }

                printf("\nDone, total time: %1.4fs.\n\n", $total_time);
            }
        }
    }
}
