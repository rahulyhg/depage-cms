<?php
/**
 * @file    framework/log/log.php
 *
 * depage log module
 *
 *
 * copyright (c) 2002-2009 Frank Hellenkamp [jonas@depagecms.net]
 *
 * @author    Frank Hellenkamp [jonas@depagecms.net]
 */

class log {
    // {{{ default config
    protected $_defaults = array(
        file => "",
    );
    protected $_options = array();
    // }}}
    
    // {{{ constructor
    /**
     * automatically loads classes from the framework or the private modules
     *
     * @param   $options (array) named options for base class
     *
     * @return  null
     */
    public function __construct($options = NULL) {
        $conf = new config($options);
        $this->_options = $conf->toOptions($this->_defaults);
    }
    // }}}
    // {{{ log
    /**
     * log a message
     *
     * @param   $arg (var) text, array or object to log
     * @param   $type (string) type of the log message
     *
     * @return  null
     */
    public function log($arg, $type = "debug") {
        if (gettype($arg) != 'string') {
            ob_start();
            print_r($arg);
            $message .= ob_get_contents();
            ob_end_clean();
        } else {
            $message .= $arg;
        }

        $message = str_replace("\n", "\n    ", rtrim($message, "\n"));
        $date = date("c");

        if ($this->_options['file'] != "") {
            error_log("[$date] [$type] $message\n", 3, $this->_options['file']);
        } else {
            error_log("[$date] [$type] $message\n");
        }
    }
    // }}}
}

/* vim:set ft=php fenc=UTF-8 sw=4 sts=4 fdm=marker et : */