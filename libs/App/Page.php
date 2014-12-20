<?php

/*
 * This file is part of the 'octris/core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Core\App;

use \Octris\Core\App as app;
use \Octris\Core\Provider as provider;

/**
 * Core page controller class.
 *
 * @copyright   copyright (c) 2011-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class Page
{
    /**
     * Next valid actions and their view pages.
     *
     * @type    array
     */
    protected $next_pages = array();

    /**
     * Stored error Messages occured during execution of the current page.
     *
     * @type    array
     */
    protected $errors = array();

    /**
     * Stored notification messages collected during execution of the current page.
     *
     * @type    array
     */
    protected $messages = array();

    /**
     * Application instance.
     *
     * @type    \Octris\Core\App
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param   \Octris\Core\App                        Application instance.
     */
    public function __construct(\Octris\Core\App $app)
    {
        $this->app = $app;
    }

    /**
     * Added magic getter to provide readonly access to protected properties.
     *
     * @param   string          $name                   Name of property to return.
     * @return  mixed                                   Value of property.
     */
    public function __get($name)
    {
        return (isset($this->{$name}) ? $this->{$name} : null);
    }

    /**
     * Returns name of page class if page instance is casted to a string.
     *
     * @param   string                                  Returns name of class.
     */
    final public function __toString()
    {
        return get_called_class();
    }

    /**
     * Add a validator for the page.
     *
     * @param   string                          $type           Name of data to access through data provider.
     * @param   string                          $action         Action that triggers the validator.
     * @param   array                           $schema         Validation schema.
     * @param   int                             $mode           Validation mode.
     */
    protected function addValidator($type, $action, array $schema, $mode = \Octris\Core\Validate\Schema::T_IGNORE)
    {
        provider::access($type)->addValidator((string)$this . ':' . $action, $schema);
    }

    /**
     * Apply a configured validator.
     *
     * @param   string                          $type           Name of data to access through data provider.
     * @param   string                          $action         Action to apply validator for.
     * @return  mixed                           Returns true, if valid otherwise an array with error messages.
     */
    protected function applyValidator($type, $action)
    {
        $provider = provider::access($type);
        $key      = (string)$this . ':' . $action;

        return ($provider->hasValidator($key)
                ? $provider->applyValidator($key)
                : array(true, null, array(), null));
    }

    /**
     * Apply validation ruleset.
     *
     * @param   string                          $action         Action to select ruleset for.
     * @return  bool                                            Returns true if validation suceeded, otherwise false.
     */
    public function validate($action)
    {
        $is_valid = true;

        if ($action != '') {
            $method = \Octris\Core\App\Web\Request::getRequestMethod();

            list($is_valid, , $errors, $validator) = $this->applyValidator($method, $action);

            $this->addErrors($errors);
        }

        return $is_valid;
    }

    /**
     * Gets next page from action and next_pages array of last page
     *
     * @param   string                          $action         Action to get next page for.
     * @param   string                          $entry_page     Name of the entry page for possible fallback.
     * @return  \Octris\Core\App\Page                       Next page.
     */
    public function getNextPage($action, $entry_page)
    {
        $next = $this;

        if (count($this->errors) == 0) {
            if (isset($this->next_pages[$action])) {
                // lookup next page from current page's next_page array
                $class = $this->next_pages[$action];
                $next  = new $class($this->app);
            } else {
                // lookup next page from entry page's next_page array
                $entry = new $entry_page($this->app);

                if (isset($entry->next_pages[$action])) {
                    $class = $entry->next_pages[$action];
                    $next  = new $class($this->app);
                }
            }
        }

        return $next;
    }

    /**
     * Add error message for current page.
     *
     * @param   string          $err                        Error message to add.
     */
    public function addError($err)
    {
        $this->errors[] = $err;
    }

    /**
     * Add multiple errors for current page.
     *
     * @param   array           $err                        Array of error messages.
     */
    public function addErrors(array $err)
    {
        $this->errors = array_merge($this->errors, $err);
    }

    /**
     * Add message for current page.
     *
     * @param   string          $msg                        Message to add.
     */
    public function addMessage($msg)
    {
        $this->messages[] = $msg;
    }

    /**
     * Add multiple messages for current page.
     *
     * @param   array           $msg                        Array of messages.
     */
    public function addMessages(array $msg)
    {
        $this->messages = array_merge($this->messages, $msg);
    }

    /**
     * Determine the action of the request.
     *
     * @return  string                                      Name of action
     */
    abstract public function getAction();

    /**
     * Abstract method definition.
     *
     * @param   \Octris\Core\App\Page       $last_page      Instance of last called page.
     * @param   string                          $action         Action that led to current page.
     * @return  mixed                                           Returns either page to redirect to or null.
     * @abstract
     */
    abstract public function prepare(\Octris\Core\App\Page $last_page, $action);

    /**
     * Abstract method definition.
     *
     * @abstract
     */
    abstract public function render();
    /**/
}
